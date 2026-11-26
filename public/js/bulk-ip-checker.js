/**
 * Bulk Website IP Checker — High-Throughput Client Batch Engine & Cluster Analyzer
 * Supports 10,000+ domains via concurrent chunked API calls, global Same-IP / Class-C
 * clustering, zero-lag paginated DOM rendering (XSS-safe textContent), and multi-format exports.
 */
(function () {
    'use strict';

    const cfg = window.BULK_IP_CHECKER_CONFIG || {};
    const RESOLVE_URL = String(cfg.resolveUrl || '/services/bulk-website-ip-checker/resolve').replace(/\/+$/, '');
    const MAX_BATCH_SIZE = Number(cfg.maxBatchSize) || 50;
    const DEFAULT_BATCH_SIZE = Math.min(Number(cfg.defaultBatchSize) || 30, MAX_BATCH_SIZE);
    const DEFAULT_CONCURRENCY = Number(cfg.defaultConcurrency) || 3;
    const SAMPLE_DOMAINS = Array.isArray(cfg.sampleDomains) && cfg.sampleDomains.length
        ? cfg.sampleDomains
        : [
            'shrawaneffects.com',
            'shyamoengineers.com',
            'https://google.com/search?q=dns',
            'cloudflare.com',
            'github.com',
            'amazon.com',
            'microsoft.com',
            'digitalocean.com',
            'ipv6.google.com',
            'nonexistent-domain-check-example.invalid'
        ];

    const CSV_HEADER_WORDS = new Set([
        'domain', 'domains', 'url', 'urls', 'website', 'websites',
        'host', 'hosts', 'hostname', 'site', 'sites', 'ip', 'ip_address',
        'address', 'target', 'id', 'no', 'sr', 'index', 'rank'
    ]);

    const UNSUPPORTED_SCHEME_RE = /^(javascript|data|vbscript|mailto|tel|file|about|blob):/i;

    const state = {
        status: 'idle', // idle | running | paused | stopped | completed
        batches: [],
        completedBatchesCount: 0,
        batchSize: DEFAULT_BATCH_SIZE,
        concurrency: DEFAULT_CONCURRENCY,
        deduplicate: true,
        enrichMetadata: true,
        nextBatchIndex: 0,
        failedBatches: [],
        totalQueued: 0,
        processedCount: 0,
        startedAt: 0,
        pausedAt: null,
        pausedDurationMs: 0,
        activeWorkers: 0,
        abortControllers: new Set(),
        resumeResolvers: [],
        results: [],
        clusters: {
            byIp: [],
            byClassC: [],
            ipMap: new Map(),
            classCMap: new Map(),
            typeCounts: {
                cdn_proxy: 0,
                cloud: 0,
                datacenter: 0,
                hosting_isp: 0,
                private_bogon: 0,
                unresolved: 0
            },
            resolvedCount: 0,
            unresolvedCount: 0,
            invalidCount: 0,
            uniqueIpCount: 0,
            sharedIpHostCount: 0
        },
        filter: {
            chip: 'all',
            search: '',
            ip: null,
            classC: null,
            ipType: null
        },
        sort: {
            key: 'seqIndex',
            dir: 'asc'
        },
        page: 1,
        perPage: 50,
        activeTab: 'results', // results | clusters
        clusterSubTab: 'by_ip', // by_ip | by_class_c
        showUniqueClusters: false,
        clusterSearch: '',
        clusterPage: 1,
        clustersPerPage: 50,
        expandedClusters: new Set(),
        filteredIndices: [],
        renderTimer: null,
        etaTimer: null,
        parsedSummary: { rawCount: 0, uniqueHosts: [], entries: [] }
    };

    // DOM Cache
    const dom = {};

    function cacheDom() {
        const ids = [
            'domainsInput', 'fileDropZone', 'fileInput', 'browseFileBtn',
            'loadSampleBtn', 'clearInputBtn', 'deduplicateToggle', 'enrichMetadataToggle',
            'batchSizeSelect', 'concurrencySelect',
            'statRawLines', 'statUniqueHosts', 'statEstBatches',
            'startScanBtn', 'pauseScanBtn', 'resumeScanBtn', 'stopScanBtn',
            'retryFailedBtn', 'resetResultsBtn', 'statusBanner',
            'progressSection', 'progressBar', 'progressText', 'speedText',
            'etaText', 'batchProgressText', 'workerStatusBadge',
            'kpiSection', 'kpiProcessed', 'kpiProcessedSub', 'kpiUniqueIps',
            'kpiSharedIps', 'kpiSharedIpsSub', 'kpiSharedClassC', 'kpiSharedClassCSub',
            'kpiInfraMix', 'kpiInfraMixSub',
            'workspaceSection', 'tabResultsBtn', 'tabClustersBtn',
            'tabResultsCount', 'tabClustersCount', 'resultsTabPane', 'clustersTabPane',
            'tableSearchInput', 'clearSearchBtn', 'perPageSelect', 'filterChipsRow',
            'activeDrilldownBar', 'activeDrilldownText', 'clearDrilldownBtn',
            'resultsTableHead', 'resultsTableBody', 'resultsEmptyState',
            'paginationSummary', 'paginationControls',
            'clusterByIpBtn', 'clusterByClassCBtn', 'clusterByIpBadge', 'clusterByClassCBadge',
            'clusterSearchInput', 'showUniqueClustersToggle', 'clustersContainer',
            'clusterPaginationSummary', 'clusterPaginationControls',
            'exportCsvAllBtn', 'exportCsvSharedBtn', 'exportCsvClustersBtn',
            'exportJsonBtn', 'copySharedBtn', 'copyDomainsTxtBtn', 'actionToast', 'actionToastMsg'
        ];
        ids.forEach(function (id) {
            dom[id] = document.getElementById(id);
        });
    }

    // -------------------------------------------------------------------------
    // Safe DOM Creation & Helpers
    // -------------------------------------------------------------------------
    function el(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined && text !== null) node.textContent = String(text);
        return node;
    }

    function clearEl(node) {
        if (!node) return;
        while (node.firstChild) {
            node.removeChild(node.firstChild);
        }
    }

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return (meta && meta.getAttribute('content')) || cfg.csrfToken || '';
    }

    let toastTimeout = null;
    function showToast(message, variant) {
        if (!dom.actionToast || !dom.actionToastMsg) return;
        dom.actionToastMsg.textContent = message;
        dom.actionToast.classList.remove('d-none', 'border-success', 'border-warning', 'border-danger', 'border-info');
        dom.actionToast.classList.add('border-' + (variant || 'success'));
        clearTimeout(toastTimeout);
        toastTimeout = setTimeout(function () {
            dom.actionToast.classList.add('d-none');
        }, 3200);
    }

    function showStatusBanner(message, type) {
        if (!dom.statusBanner) return;
        if (!message) {
            dom.statusBanner.classList.add('d-none');
            dom.statusBanner.textContent = '';
            return;
        }
        dom.statusBanner.className = 'alert font-mono small mb-3 d-flex align-items-center gap-2 alert-' + (type || 'info');
        clearEl(dom.statusBanner);
        const icon = el('i', type === 'danger' ? 'bi bi-exclamation-octagon-fill' : (type === 'warning' ? 'bi bi-exclamation-triangle-fill' : 'bi bi-info-circle-fill'));
        const span = el('span', '', message);
        dom.statusBanner.appendChild(icon);
        dom.statusBanner.appendChild(span);
    }

    function copyTextToClipboard(text, successMsg) {
        if (!text) {
            showToast('Nothing to copy.', 'warning');
            return;
        }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(function () {
                showToast(successMsg || 'Copied to clipboard.', 'success');
            }).catch(function () {
                fallbackCopy(text, successMsg);
            });
        } else {
            fallbackCopy(text, successMsg);
        }
    }

    function fallbackCopy(text, successMsg) {
        const ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.opacity = '0';
        document.body.appendChild(ta);
        ta.focus();
        ta.select();
        try {
            document.execCommand('copy');
            showToast(successMsg || 'Copied to clipboard.', 'success');
        } catch (err) {
            showToast('Clipboard access blocked by browser.', 'warning');
        }
        document.body.removeChild(ta);
    }

    // -------------------------------------------------------------------------
    // Input Normalization & CSV/TXT Parsing
    // -------------------------------------------------------------------------
    function isValidIpv4(str) {
        if (!/^\d{1,3}(\.\d{1,3}){3}$/.test(str)) return false;
        const parts = str.split('.');
        for (let i = 0; i < 4; i++) {
            const n = Number(parts[i]);
            if (n < 0 || n > 255) return false;
        }
        return true;
    }

    function normalizeHostClient(raw) {
        if (!raw) return '';
        let s = String(raw).trim();
        s = s.replace(/^["'`]+|["'`,;]+$/g, '').trim();
        if (!s) return '';
        if (UNSUPPORTED_SCHEME_RE.test(s)) return '';

        // Strip protocol
        s = s.replace(/^[a-zA-Z][a-zA-Z0-9+\-.]*:\/\//, '');
        s = s.replace(/^\/+/, '');

        // Strip credentials if present before first slash
        const slashIdx = s.indexOf('/');
        const qIdx = s.indexOf('?');
        const hashIdx = s.indexOf('#');
        let cut = s.length;
        if (slashIdx !== -1 && slashIdx < cut) cut = slashIdx;
        if (qIdx !== -1 && qIdx < cut) cut = qIdx;
        if (hashIdx !== -1 && hashIdx < cut) cut = hashIdx;
        s = s.slice(0, cut);

        const atIdx = s.lastIndexOf('@');
        if (atIdx !== -1) {
            s = s.slice(atIdx + 1);
        }

        // Bracketed IPv6 e.g. [2a07:7800::189]:8080
        if (s.startsWith('[')) {
            const closeIdx = s.indexOf(']');
            if (closeIdx !== -1) {
                s = s.slice(1, closeIdx);
            }
        } else {
            // Bare IPv6 has >=2 colons; hostname:port has 1 colon
            const firstColon = s.indexOf(':');
            if (firstColon !== -1 && firstColon === s.lastIndexOf(':')) {
                s = s.slice(0, firstColon);
            }
        }

        s = s.replace(/\.+$/, '').toLowerCase().trim();
        if (!s) return '';

        // Punycode conversion for internationalized domains
        if (/[^\x00-\x7F]/.test(s)) {
            try {
                const parsed = new URL('http://' + s);
                if (parsed && parsed.hostname) {
                    s = parsed.hostname.toLowerCase();
                }
            } catch (err) {
                // Keep original lowered string; backend validates accurately
            }
        }
        return s;
    }

    function isLikelyHostOrUrl(token) {
        const cleaned = token.replace(/^["'`]+|["'`]+$/g, '').trim();
        if (!cleaned || /\s/.test(cleaned)) return false;
        if (UNSUPPORTED_SCHEME_RE.test(cleaned)) return false;
        const lower = cleaned.toLowerCase();
        if (CSV_HEADER_WORDS.has(lower)) return false;
        if (/^https?:\/\//i.test(cleaned)) return true;
        const host = normalizeHostClient(cleaned);
        if (!host) return false;
        if (isValidIpv4(host) || (host.indexOf(':') !== -1 && /^[0-9a-f:]+$/i.test(host))) return true;
        return host.indexOf('.') !== -1 && /^[a-z0-9.\-]+$/i.test(host);
    }

    function splitCsvRow(line) {
        const cells = [];
        let cur = '';
        let inQuotes = false;
        for (let i = 0; i < line.length; i++) {
            const ch = line[i];
            if (ch === '"') {
                inQuotes = !inQuotes;
            } else if (!inQuotes && (ch === ',' || ch === ';' || ch === '\t')) {
                cells.push(cur.trim());
                cur = '';
            } else {
                cur += ch;
            }
        }
        cells.push(cur.trim());
        return cells;
    }

    function parseInputLines(text) {
        const entries = [];
        const uniqueSet = new Set();
        const uniqueHosts = [];

        if (!text) {
            return { rawCount: 0, entries: entries, uniqueHosts: uniqueHosts };
        }

        const lines = String(text).replace(/^\uFEFF/, '').split(/\r?\n/);
        for (let i = 0; i < lines.length; i++) {
            const line = lines[i].trim();
            if (!line || line.startsWith('#') || line.startsWith('//')) continue;
            if (CSV_HEADER_WORDS.has(line.replace(/^["']+|["']+$/g, '').toLowerCase())) continue;

            if (UNSUPPORTED_SCHEME_RE.test(line)) {
                const key = line.toLowerCase();
                entries.push({ raw: line, host: key });
                if (!uniqueSet.has(key)) {
                    uniqueSet.add(key);
                    uniqueHosts.push(line);
                }
                continue;
            }

            if (line.indexOf(',') !== -1 || line.indexOf(';') !== -1 || line.indexOf('\t') !== -1) {
                const cells = splitCsvRow(line);
                const nonEmpty = [];
                for (let c = 0; c < cells.length; c++) {
                    const cell = cells[c].replace(/^["']+|["']+$/g, '').trim();
                    if (cell) nonEmpty.push(cell);
                }
                if (nonEmpty.length === 0) continue;

                // Extract cells that look like URLs/domains/IPs
                let foundInRow = 0;
                for (let c = 0; c < nonEmpty.length; c++) {
                    if (isLikelyHostOrUrl(nonEmpty[c])) {
                        const host = normalizeHostClient(nonEmpty[c]);
                        entries.push({ raw: nonEmpty[c], host: host || nonEmpty[c] });
                        if (host && !uniqueSet.has(host)) {
                            uniqueSet.add(host);
                            uniqueHosts.push(host);
                        }
                        foundInRow++;
                    }
                }
                // If single non-header token wasn't matched (e.g., user testing an invalid token with trailing comma)
                if (foundInRow === 0 && nonEmpty.length === 1 && !CSV_HEADER_WORDS.has(nonEmpty[0].toLowerCase())) {
                    const rawTok = nonEmpty[0];
                    const host = normalizeHostClient(rawTok) || rawTok.toLowerCase();
                    entries.push({ raw: rawTok, host: host });
                    if (!uniqueSet.has(host)) {
                        uniqueSet.add(host);
                        uniqueHosts.push(host);
                    }
                }
            } else {
                // Space-separated tokens on one line? Only split if multiple tokens look like domains/URLs
                const spaceTokens = line.split(/\s+/).filter(Boolean);
                if (spaceTokens.length > 1 && spaceTokens.every(isLikelyHostOrUrl)) {
                    for (let t = 0; t < spaceTokens.length; t++) {
                        const tok = spaceTokens[t];
                        const host = normalizeHostClient(tok) || tok.toLowerCase();
                        entries.push({ raw: tok, host: host });
                        if (!uniqueSet.has(host)) {
                            uniqueSet.add(host);
                            uniqueHosts.push(host);
                        }
                    }
                } else {
                    const host = normalizeHostClient(line) || line.toLowerCase();
                    entries.push({ raw: line, host: host });
                    if (!uniqueSet.has(host)) {
                        uniqueSet.add(host);
                        uniqueHosts.push(host);
                    }
                }
            }
        }

        return {
            rawCount: entries.length,
            entries: entries,
            uniqueHosts: uniqueHosts
        };
    }

    function updateInputCounts() {
        const text = dom.domainsInput ? dom.domainsInput.value : '';
        const parsed = parseInputLines(text);
        state.parsedSummary = parsed;

        const dedup = dom.deduplicateToggle ? dom.deduplicateToggle.checked : true;
        const batchSize = dom.batchSizeSelect ? Math.min(Number(dom.batchSizeSelect.value) || DEFAULT_BATCH_SIZE, MAX_BATCH_SIZE) : DEFAULT_BATCH_SIZE;
        const targetCount = dedup ? parsed.uniqueHosts.length : parsed.rawCount;
        const estBatches = targetCount > 0 ? Math.ceil(targetCount / batchSize) : 0;

        if (dom.statRawLines) dom.statRawLines.textContent = parsed.rawCount.toLocaleString();
        if (dom.statUniqueHosts) dom.statUniqueHosts.textContent = parsed.uniqueHosts.length.toLocaleString();
        if (dom.statEstBatches) dom.statEstBatches.textContent = estBatches.toLocaleString();
    }

    // -------------------------------------------------------------------------
    // Batch Queue Execution & Worker Pool
    // -------------------------------------------------------------------------
    function chunkBatches(arr, size) {
        const chunks = [];
        for (let i = 0; i < arr.length; i += size) {
            chunks.push({
                baseSeqIndex: i,
                domains: arr.slice(i, i + size)
            });
        }
        return chunks;
    }

    function getActiveElapsedMs() {
        if (!state.startedAt) return 0;
        const now = state.pausedAt !== null ? state.pausedAt : Date.now();
        return Math.max(1, now - state.startedAt - state.pausedDurationMs);
    }

    function updateProgressUi() {
        if (!dom.progressSection) return;
        const total = state.totalQueued;
        const done = state.processedCount;
        const pct = total > 0 ? Math.min(100, Math.round((done / total) * 100)) : 0;

        if (dom.progressBar) {
            dom.progressBar.style.width = pct + '%';
            dom.progressBar.setAttribute('aria-valuenow', String(pct));
            dom.progressBar.textContent = pct >= 8 ? pct + '%' : '';
            if (state.status === 'running') {
                dom.progressBar.classList.add('progress-bar-striped', 'progress-bar-animated');
            } else {
                dom.progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
            }
        }

        if (dom.progressText) {
            dom.progressText.textContent = done.toLocaleString() + ' / ' + total.toLocaleString() + ' hosts (' + pct + '%)';
        }

        const elapsedSec = getActiveElapsedMs() / 1000;
        const speed = done > 0 && elapsedSec > 0.1 ? (done / elapsedSec) : 0;
        if (dom.speedText) {
            dom.speedText.textContent = speed > 0 ? speed.toFixed(1) + ' domains/sec' : '0.0 domains/sec';
        }

        if (dom.etaText) {
            if (state.status === 'completed' || done >= total) {
                dom.etaText.textContent = 'Done in ' + formatDuration(elapsedSec);
            } else if (state.status === 'paused') {
                dom.etaText.textContent = 'Paused';
            } else if (state.status === 'stopped') {
                dom.etaText.textContent = 'Stopped (' + formatDuration(elapsedSec) + ')';
            } else if (speed > 0.1) {
                const remSec = Math.max(0, (total - done) / speed);
                dom.etaText.textContent = 'ETA ' + formatDuration(remSec);
            } else {
                dom.etaText.textContent = 'Calculating ETA...';
            }
        }

        if (dom.batchProgressText) {
            dom.batchProgressText.textContent = 'Batch ' + state.completedBatchesCount.toLocaleString() + ' / ' + state.batches.length.toLocaleString() +
                (state.failedBatches.length ? ' (' + state.failedBatches.length + ' failed)' : '');
        }

        if (dom.workerStatusBadge) {
            const labelMap = {
                idle: 'READY',
                running: state.activeWorkers + ' WORKERS ACTIVE',
                paused: 'PAUSED',
                stopped: 'STOPPED',
                completed: 'COMPLETED'
            };
            dom.workerStatusBadge.textContent = labelMap[state.status] || 'READY';
        }
    }

    function formatDuration(sec) {
        const s = Math.round(sec);
        if (s < 60) return s + 's';
        const m = Math.floor(s / 60);
        const rem = s % 60;
        if (m < 60) return m + 'm ' + (rem < 10 ? '0' : '') + rem + 's';
        const h = Math.floor(m / 60);
        return h + 'h ' + (m % 60) + 'm';
    }

    function syncControlButtons() {
        const isRunning = state.status === 'running';
        const isPaused = state.status === 'paused';
        const isBusy = isRunning || isPaused;

        if (dom.startScanBtn) dom.startScanBtn.disabled = isBusy;
        if (dom.pauseScanBtn) {
            dom.pauseScanBtn.classList.toggle('d-none', !isRunning);
        }
        if (dom.resumeScanBtn) {
            dom.resumeScanBtn.classList.toggle('d-none', !isPaused);
        }
        if (dom.stopScanBtn) {
            dom.stopScanBtn.classList.toggle('d-none', !isBusy);
        }
        if (dom.retryFailedBtn) {
            dom.retryFailedBtn.classList.toggle('d-none', isBusy || state.failedBatches.length === 0);
            if (state.failedBatches.length > 0) {
                dom.retryFailedBtn.textContent = 'Retry Failed (' + state.failedBatches.length + ')';
            }
        }
        if (dom.resetResultsBtn) {
            dom.resetResultsBtn.classList.toggle('d-none', isBusy || state.results.length === 0);
        }
    }

    function sleepMs(ms) {
        return new Promise(function (resolve) {
            setTimeout(resolve, ms);
        });
    }

    function waitIfPaused() {
        if (state.status !== 'paused') return Promise.resolve();
        return new Promise(function (resolve) {
            state.resumeResolvers.push(resolve);
        });
    }

    async function fetchBatch(domains, controller) {
        const timer = setTimeout(function () {
            controller.abort();
        }, 45000);

        try {
            const resp = await fetch(RESOLVE_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                signal: controller.signal,
                body: JSON.stringify({
                    domains: domains,
                    deduplicate: state.deduplicate,
                    enrich_metadata: state.enrichMetadata
                })
            });

            if (resp.status === 419) {
                const err = new Error('CSRF session expired (419). Reload the page and retry.');
                err.fatal = true;
                throw err;
            }
            if (resp.status === 422) {
                const body = await resp.json().catch(function () { return {}; });
                const err = new Error(body.message || 'Validation error (422) on batch.');
                err.noRetry = true;
                throw err;
            }
            if (!resp.ok) {
                const err = new Error('HTTP ' + resp.status);
                err.status = resp.status;
                throw err;
            }
            const json = await resp.json();
            if (!json || !Array.isArray(json.results)) {
                throw new Error('Invalid JSON response schema.');
            }
            return json;
        } finally {
            clearTimeout(timer);
        }
    }

    async function executeBatchWithRetry(batch) {
        const baseSeqIndex = batch.baseSeqIndex;
        const domains = batch.domains;
        const maxAttempts = 3;
        let lastError = null;

        for (let attempt = 1; attempt <= maxAttempts; attempt++) {
            await waitIfPaused();
            if (state.status === 'stopped') return;

            const controller = new AbortController();
            state.abortControllers.add(controller);

            try {
                const data = await fetchBatch(domains, controller);
                state.abortControllers.delete(controller);
                ingestBatchResults(baseSeqIndex, data.results);
                return;
            } catch (err) {
                state.abortControllers.delete(controller);
                if (state.status === 'stopped') return;
                lastError = err;

                if (err.fatal) {
                    state.status = 'stopped';
                    abortAllInFlight();
                    showStatusBanner(err.message, 'danger');
                    state.failedBatches.push({ baseSeqIndex: baseSeqIndex, domains: domains, error: err.message });
                    return;
                }
                if (err.noRetry || attempt === maxAttempts) {
                    break;
                }
                const backoff = err.status === 429 ? 2500 * attempt : (attempt === 1 ? 750 : 1800);
                await sleepMs(backoff);
            }
        }

        // Permanent failure for this batch after retries: synthesize error entries so count stays accurate
        const msg = lastError ? lastError.message : 'Batch request failed';
        state.failedBatches.push({ baseSeqIndex: baseSeqIndex, domains: domains, error: msg });
        const fallbackRows = domains.map(function (d) {
            return {
                input: d,
                host: normalizeHostClient(d) || d,
                valid: false,
                status: 'unresolved',
                error: 'Batch error: ' + msg,
                ipv4: null,
                ipv4_all: [],
                ipv6: null,
                ipv6_all: [],
                cname: null,
                primary_ip: null,
                ip_version: null,
                ip_version_label: null,
                ip_class: null,
                ip_scope: null,
                class_c: null,
                class_c_prefix: null,
                class_b: null,
                ipv6_subnet_64: null,
                ip_type: 'unresolved',
                ip_type_label: 'Unresolved',
                provider: null,
                asn: null,
                asn_number: null,
                bgp_prefix: null,
                country: null,
                org: null,
                rdns: null,
                cached: false
            };
        });
        ingestBatchResults(baseSeqIndex, fallbackRows);
    }

    function ingestBatchResults(baseSeqIndex, rows) {
        for (let i = 0; i < rows.length; i++) {
            const r = rows[i];
            r.seqIndex = baseSeqIndex + i;
            state.results.push(r);
        }
        state.completedBatchesCount++;
        state.processedCount += rows.length;
        updateProgressUi();
        scheduleRecomputeAndRender();
    }

    function scheduleRecomputeAndRender() {
        if (state.renderTimer) return;
        state.renderTimer = setTimeout(function () {
            state.renderTimer = null;
            recomputeAndRender();
        }, 220);
    }

    function abortAllInFlight() {
        state.abortControllers.forEach(function (ctrl) {
            try { ctrl.abort(); } catch (e) {}
        });
        state.abortControllers.clear();
    }

    async function workerLoop() {
        state.activeWorkers++;
        updateProgressUi();
        try {
            while (true) {
                await waitIfPaused();
                if (state.status === 'stopped') break;
                const idx = state.nextBatchIndex++;
                if (idx >= state.batches.length) break;
                await executeBatchWithRetry(state.batches[idx]);
            }
        } finally {
            state.activeWorkers--;
            updateProgressUi();
        }
    }

    async function startScan() {
        updateInputCounts();
        const parsed = state.parsedSummary;
        const dedup = dom.deduplicateToggle ? dom.deduplicateToggle.checked : true;
        const queue = dedup ? parsed.uniqueHosts.slice() : parsed.entries.map(function (e) { return e.raw; });

        if (queue.length === 0) {
            showStatusBanner('Please enter at least one website URL, domain, or IP address (or load Sample URLs).', 'warning');
            if (dom.domainsInput) dom.domainsInput.focus();
            return;
        }

        showStatusBanner('');
        state.deduplicate = dedup;
        state.enrichMetadata = dom.enrichMetadataToggle ? dom.enrichMetadataToggle.checked : true;
        state.batchSize = dom.batchSizeSelect ? Math.min(Number(dom.batchSizeSelect.value) || DEFAULT_BATCH_SIZE, MAX_BATCH_SIZE) : DEFAULT_BATCH_SIZE;
        state.concurrency = dom.concurrencySelect ? Math.max(1, Math.min(Number(dom.concurrencySelect.value) || DEFAULT_CONCURRENCY, 8)) : DEFAULT_CONCURRENCY;

        state.batches = chunkBatches(queue, state.batchSize);
        state.completedBatchesCount = 0;
        state.nextBatchIndex = 0;
        state.failedBatches = [];
        state.totalQueued = queue.length;
        state.processedCount = 0;
        state.results = [];
        state.expandedClusters.clear();
        state.filter = { chip: 'all', search: '', ip: null, classC: null, ipType: null };
        if (dom.tableSearchInput) dom.tableSearchInput.value = '';
        state.page = 1;
        state.clusterPage = 1;
        state.startedAt = Date.now();
        state.pausedAt = null;
        state.pausedDurationMs = 0;
        state.status = 'running';

        if (dom.progressSection) dom.progressSection.classList.remove('d-none');
        if (dom.kpiSection) dom.kpiSection.classList.remove('d-none');
        if (dom.workspaceSection) dom.workspaceSection.classList.remove('d-none');

        syncControlButtons();
        recomputeAndRender();
        updateProgressUi();

        clearInterval(state.etaTimer);
        state.etaTimer = setInterval(updateProgressUi, 500);

        const workerCount = Math.min(state.concurrency, state.batches.length);
        const workers = [];
        for (let w = 0; w < workerCount; w++) {
            workers.push(workerLoop());
        }

        await Promise.all(workers);

        clearInterval(state.etaTimer);
        state.etaTimer = null;
        if (state.renderTimer) {
            clearTimeout(state.renderTimer);
            state.renderTimer = null;
        }

        if (state.status === 'running') {
            state.status = 'completed';
        }
        syncControlButtons();
        recomputeAndRender();
        updateProgressUi();

        if (state.failedBatches.length > 0 && state.status !== 'stopped') {
            showStatusBanner(state.failedBatches.length + ' batch(es) encountered errors after automatic retries. You can click "Retry Failed" above.', 'warning');
        } else if (state.status === 'completed') {
            showToast('Scan complete: ' + state.results.length.toLocaleString() + ' hosts analyzed.', 'success');
        }
    }

    function pauseScan() {
        if (state.status !== 'running') return;
        state.status = 'paused';
        state.pausedAt = Date.now();
        syncControlButtons();
        updateProgressUi();
    }

    function resumeScan() {
        if (state.status !== 'paused') return;
        if (state.pausedAt !== null) {
            state.pausedDurationMs += Date.now() - state.pausedAt;
            state.pausedAt = null;
        }
        state.status = 'running';
        const resolvers = state.resumeResolvers.splice(0);
        resolvers.forEach(function (fn) { fn(); });
        syncControlButtons();
        updateProgressUi();
    }

    function stopScan() {
        if (state.status !== 'running' && state.status !== 'paused') return;
        if (state.pausedAt !== null) {
            state.pausedDurationMs += Date.now() - state.pausedAt;
            state.pausedAt = null;
        }
        state.status = 'stopped';
        const resolvers = state.resumeResolvers.splice(0);
        resolvers.forEach(function (fn) { fn(); });
        abortAllInFlight();
        clearInterval(state.etaTimer);
        state.etaTimer = null;
        syncControlButtons();
        recomputeAndRender();
        updateProgressUi();
        showToast('Scan stopped. Showing ' + state.results.length.toLocaleString() + ' collected results.', 'info');
    }

    async function retryFailedBatches() {
        if (state.failedBatches.length === 0 || state.status === 'running') return;
        const toRetry = state.failedBatches.splice(0);
        const failedHostSet = new Set();
        toRetry.forEach(function (b) {
            b.domains.forEach(function (d) {
                failedHostSet.add(normalizeHostClient(d) || d);
            });
        });

        // Remove fallback error rows from prior failed batches
        state.results = state.results.filter(function (r) {
            return !(r.error && String(r.error).startsWith('Batch error:') && failedHostSet.has(r.host || r.input));
        });
        state.processedCount = state.results.length;

        state.batches = toRetry.map(function (b) {
            return { baseSeqIndex: b.baseSeqIndex, domains: b.domains };
        });
        state.completedBatchesCount = 0;
        state.nextBatchIndex = 0;
        state.status = 'running';
        syncControlButtons();
        updateProgressUi();

        clearInterval(state.etaTimer);
        state.etaTimer = setInterval(updateProgressUi, 500);

        const workerCount = Math.min(state.concurrency, state.batches.length);
        const workers = [];
        for (let w = 0; w < workerCount; w++) {
            workers.push(workerLoop());
        }
        await Promise.all(workers);

        clearInterval(state.etaTimer);
        if (state.status === 'running') state.status = 'completed';
        syncControlButtons();
        recomputeAndRender();
        updateProgressUi();
    }

    function resetResults() {
        if (state.status === 'running' || state.status === 'paused') return;
        state.status = 'idle';
        state.results = [];
        state.failedBatches = [];
        state.batches = [];
        state.processedCount = 0;
        state.totalQueued = 0;
        state.expandedClusters.clear();
        state.filter = { chip: 'all', search: '', ip: null, classC: null, ipType: null };
        if (dom.progressSection) dom.progressSection.classList.add('d-none');
        if (dom.kpiSection) dom.kpiSection.classList.add('d-none');
        if (dom.workspaceSection) dom.workspaceSection.classList.add('d-none');
        showStatusBanner('');
        syncControlButtons();
    }

    // -------------------------------------------------------------------------
    // Global Same-IP & Class-C Clustering (Strictly status === 'resolved' only)
    // -------------------------------------------------------------------------
    function recomputeGlobalClusters() {
        const ipMap = new Map();
        const classCMap = new Map();
        const typeCounts = {
            cdn_proxy: 0,
            cloud: 0,
            datacenter: 0,
            hosting_isp: 0,
            private_bogon: 0,
            unresolved: 0
        };
        let resolvedCount = 0;
        let unresolvedCount = 0;
        let invalidCount = 0;

        for (let i = 0; i < state.results.length; i++) {
            const item = state.results[i];
            if (item.status === 'resolved') {
                resolvedCount++;
                const t = item.ip_type && typeCounts.hasOwnProperty(item.ip_type) ? item.ip_type : 'hosting_isp';
                typeCounts[t]++;

                const ipKey = item.primary_ip || item.ipv4 || item.ipv6 || null;
                const hostKey = (item.host || item.input || '').toLowerCase();

                if (ipKey) {
                    let group = ipMap.get(ipKey);
                    if (!group) {
                        group = {
                            ip: ipKey,
                            class_c: item.class_c || null,
                            ip_type: item.ip_type || 'hosting_isp',
                            ip_type_label: item.ip_type_label || 'Hosting / ISP',
                            provider: item.provider || null,
                            asn: item.asn || null,
                            org: item.org || null,
                            country: item.country || null,
                            rdns: item.rdns || null,
                            hostsSet: new Set(),
                            hosts: [],
                            rowIndices: []
                        };
                        ipMap.set(ipKey, group);
                    } else {
                        if (!group.provider && item.provider) group.provider = item.provider;
                        if (!group.org && item.org) group.org = item.org;
                        if (!group.asn && item.asn) group.asn = item.asn;
                        if (!group.country && item.country) group.country = item.country;
                        if (!group.rdns && item.rdns) group.rdns = item.rdns;
                    }
                    if (hostKey && !group.hostsSet.has(hostKey)) {
                        group.hostsSet.add(hostKey);
                        group.hosts.push(item.host || item.input);
                    }
                    group.rowIndices.push(i);
                }

                if (item.class_c) {
                    let ccGroup = classCMap.get(item.class_c);
                    if (!ccGroup) {
                        ccGroup = {
                            class_c: item.class_c,
                            ipsSet: new Set(),
                            ips: [],
                            hostsSet: new Set(),
                            hosts: [],
                            providersSet: new Set(),
                            rowIndices: []
                        };
                        classCMap.set(item.class_c, ccGroup);
                    }
                    if (ipKey && !ccGroup.ipsSet.has(ipKey)) {
                        ccGroup.ipsSet.add(ipKey);
                        ccGroup.ips.push(ipKey);
                    }
                    if (hostKey && !ccGroup.hostsSet.has(hostKey)) {
                        ccGroup.hostsSet.add(hostKey);
                        ccGroup.hosts.push(item.host || item.input);
                    }
                    if (item.provider || item.org) {
                        ccGroup.providersSet.add(item.provider || item.org);
                    }
                    ccGroup.rowIndices.push(i);
                }
            } else {
                typeCounts.unresolved++;
                if (item.status === 'invalid') invalidCount++;
                else unresolvedCount++;
            }
        }

        // Annotate every row with global cluster counts (never flag unresolved/invalid!)
        for (let i = 0; i < state.results.length; i++) {
            const item = state.results[i];
            if (item.status === 'resolved') {
                const ipKey = item.primary_ip || item.ipv4 || item.ipv6 || null;
                const ipGroup = ipKey ? ipMap.get(ipKey) : null;
                item.globalSameIpCount = ipGroup ? ipGroup.hostsSet.size : 1;
                item.globalIsSharedIp = item.globalSameIpCount > 1;

                const ccGroup = item.class_c ? classCMap.get(item.class_c) : null;
                item.globalSameClassCCount = ccGroup ? ccGroup.hostsSet.size : (item.class_c ? 1 : 0);
                item.globalIsSameClassC = item.globalSameClassCCount > 1;
            } else {
                item.globalSameIpCount = 0;
                item.globalIsSharedIp = false;
                item.globalSameClassCCount = 0;
                item.globalIsSameClassC = false;
            }
        }

        const byIp = Array.from(ipMap.values()).map(function (g) {
            return {
                ip: g.ip,
                count: g.hostsSet.size,
                rowCount: g.rowIndices.length,
                class_c: g.class_c,
                ip_type: g.ip_type,
                ip_type_label: g.ip_type_label,
                provider: g.provider,
                asn: g.asn,
                org: g.org,
                country: g.country,
                rdns: g.rdns,
                hosts: g.hosts
            };
        }).sort(function (a, b) {
            if (b.count !== a.count) return b.count - a.count;
            return a.ip.localeCompare(b.ip);
        });

        const byClassC = Array.from(classCMap.values()).map(function (g) {
            return {
                class_c: g.class_c,
                count: g.hostsSet.size,
                ipCount: g.ipsSet.size,
                ips: g.ips,
                hosts: g.hosts,
                providers: Array.from(g.providersSet)
            };
        }).sort(function (a, b) {
            if (b.count !== a.count) return b.count - a.count;
            return a.class_c.localeCompare(b.class_c);
        });

        let sharedIpHostCount = 0;
        for (let i = 0; i < byIp.length; i++) {
            if (byIp[i].count > 1) sharedIpHostCount += byIp[i].count;
        }

        state.clusters = {
            byIp: byIp,
            byClassC: byClassC,
            ipMap: ipMap,
            classCMap: classCMap,
            typeCounts: typeCounts,
            resolvedCount: resolvedCount,
            unresolvedCount: unresolvedCount,
            invalidCount: invalidCount,
            uniqueIpCount: byIp.length,
            sharedIpHostCount: sharedIpHostCount
        };
    }

    // -------------------------------------------------------------------------
    // Filtering & Sorting
    // -------------------------------------------------------------------------
    function ipv4ToInt(ip) {
        if (!ip || !isValidIpv4(ip)) return -1;
        const p = ip.split('.');
        return (((Number(p[0]) << 24) >>> 0) + (Number(p[1]) << 16) + (Number(p[2]) << 8) + Number(p[3])) >>> 0;
    }

    function applyFilterAndSort() {
        const search = (state.filter.search || '').trim().toLowerCase();
        const chip = state.filter.chip;
        const filterIp = state.filter.ip;
        const filterClassC = state.filter.classC;
        const filterIpType = state.filter.ipType;

        const indices = [];
        for (let i = 0; i < state.results.length; i++) {
            const r = state.results[i];

            if (filterIp) {
                const pIp = r.primary_ip || r.ipv4 || r.ipv6;
                if (pIp !== filterIp && (!Array.isArray(r.ipv4_all) || r.ipv4_all.indexOf(filterIp) === -1) && (!Array.isArray(r.ipv6_all) || r.ipv6_all.indexOf(filterIp) === -1)) {
                    continue;
                }
            }
            if (filterClassC && r.class_c !== filterClassC) continue;
            if (filterIpType && r.ip_type !== filterIpType) continue;

            if (chip === 'shared_ip' && !r.globalIsSharedIp) continue;
            if (chip === 'same_class_c' && !r.globalIsSameClassC) continue;
            if (chip === 'cdn_proxy' && r.ip_type !== 'cdn_proxy') continue;
            if (chip === 'cloud_hosting' && r.ip_type !== 'cloud' && r.ip_type !== 'datacenter' && r.ip_type !== 'hosting_isp') continue;
            if (chip === 'unresolved' && r.status === 'resolved') continue;

            if (search) {
                const hay = [
                    r.host, r.input, r.ipv4, r.ipv6, r.cname, r.class_c,
                    r.ip_type_label, r.provider, r.asn, r.org, r.rdns, r.country, r.ip_class
                ].filter(Boolean).join(' ').toLowerCase();
                if (hay.indexOf(search) === -1) continue;
            }

            indices.push(i);
        }

        const key = state.sort.key;
        const dir = state.sort.dir === 'desc' ? -1 : 1;

        indices.sort(function (ia, ib) {
            const a = state.results[ia];
            const b = state.results[ib];
            let cmp = 0;

            if (key === 'seqIndex') {
                cmp = (a.seqIndex || 0) - (b.seqIndex || 0);
            } else if (key === 'globalSameIpCount') {
                cmp = (a.globalSameIpCount || 0) - (b.globalSameIpCount || 0);
            } else if (key === 'globalSameClassCCount') {
                cmp = (a.globalSameClassCCount || 0) - (b.globalSameClassCCount || 0);
            } else if (key === 'ipv4') {
                const na = ipv4ToInt(a.ipv4);
                const nb = ipv4ToInt(b.ipv4);
                if (na === -1 && nb === -1) cmp = 0;
                else if (na === -1) cmp = 1;
                else if (nb === -1) cmp = -1;
                else cmp = na - nb;
            } else if (key === 'class_c') {
                const ca = ipv4ToInt(a.class_c ? a.class_c.split('/')[0] : '');
                const cb = ipv4ToInt(b.class_c ? b.class_c.split('/')[0] : '');
                if (ca === -1 && cb === -1) cmp = 0;
                else if (ca === -1) cmp = 1;
                else if (cb === -1) cmp = -1;
                else cmp = ca - cb;
            } else {
                const va = String(a[key] || '').toLowerCase();
                const vb = String(b[key] || '').toLowerCase();
                if (!va && !vb) cmp = 0;
                else if (!va) cmp = 1;
                else if (!vb) cmp = -1;
                else cmp = va < vb ? -1 : (va > vb ? 1 : 0);
            }

            if (cmp !== 0) return cmp * dir;
            return (a.seqIndex || 0) - (b.seqIndex || 0);
        });

        state.filteredIndices = indices;
    }

    // -------------------------------------------------------------------------
    // Rendering: KPIs, Filter Chips, Results Table, Clusters, Pagination
    // -------------------------------------------------------------------------
    function renderKpis() {
        const c = state.clusters;
        const sharedIpGroups = c.byIp.filter(function (g) { return g.count > 1; }).length;
        const sharedClassCGroups = c.byClassC.filter(function (g) { return g.count > 1; }).length;

        if (dom.kpiProcessed) dom.kpiProcessed.textContent = state.results.length.toLocaleString();
        if (dom.kpiProcessedSub) {
            dom.kpiProcessedSub.textContent =
                c.resolvedCount.toLocaleString() + ' resolved • ' +
                c.unresolvedCount.toLocaleString() + ' unresolved' +
                (c.invalidCount ? ' • ' + c.invalidCount.toLocaleString() + ' invalid' : '');
        }
        if (dom.kpiUniqueIps) dom.kpiUniqueIps.textContent = c.uniqueIpCount.toLocaleString();
        if (dom.kpiSharedIps) dom.kpiSharedIps.textContent = sharedIpGroups.toLocaleString();
        if (dom.kpiSharedIpsSub) {
            dom.kpiSharedIpsSub.textContent = c.sharedIpHostCount.toLocaleString() + ' websites share an IP';
        }
        if (dom.kpiSharedClassC) dom.kpiSharedClassC.textContent = sharedClassCGroups.toLocaleString();
        if (dom.kpiSharedClassCSub) {
            dom.kpiSharedClassCSub.textContent = c.byClassC.length.toLocaleString() + ' total /24 subnets';
        }
        const hostingTotal = c.typeCounts.cloud + c.typeCounts.datacenter + c.typeCounts.hosting_isp;
        if (dom.kpiInfraMix) {
            dom.kpiInfraMix.textContent = c.typeCounts.cdn_proxy.toLocaleString() + ' / ' + hostingTotal.toLocaleString();
        }
        if (dom.kpiInfraMixSub) {
            dom.kpiInfraMixSub.textContent = 'CDN/Proxy vs Cloud/Hosting';
        }

        if (dom.tabResultsCount) dom.tabResultsCount.textContent = state.results.length.toLocaleString();
        if (dom.tabClustersCount) dom.tabClustersCount.textContent = (sharedIpGroups + sharedClassCGroups).toLocaleString();
        if (dom.clusterByIpBadge) dom.clusterByIpBadge.textContent = sharedIpGroups.toLocaleString();
        if (dom.clusterByClassCBadge) dom.clusterByClassCBadge.textContent = sharedClassCGroups.toLocaleString();
    }

    function renderFilterChips() {
        if (!dom.filterChipsRow) return;
        let sharedIpRows = 0;
        let sharedClassCRows = 0;
        let cdnRows = 0;
        let cloudHostingRows = 0;
        let unresolvedRows = 0;

        for (let i = 0; i < state.results.length; i++) {
            const r = state.results[i];
            if (r.globalIsSharedIp) sharedIpRows++;
            if (r.globalIsSameClassC) sharedClassCRows++;
            if (r.ip_type === 'cdn_proxy') cdnRows++;
            if (r.ip_type === 'cloud' || r.ip_type === 'datacenter' || r.ip_type === 'hosting_isp') cloudHostingRows++;
            if (r.status !== 'resolved') unresolvedRows++;
        }

        const chips = [
            { id: 'all', label: 'All', count: state.results.length },
            { id: 'shared_ip', label: 'Shared IP Only', count: sharedIpRows },
            { id: 'same_class_c', label: 'Same Class C Only', count: sharedClassCRows },
            { id: 'cdn_proxy', label: 'CDN / Proxy', count: cdnRows },
            { id: 'cloud_hosting', label: 'Cloud / Hosting', count: cloudHostingRows },
            { id: 'unresolved', label: 'Unresolved', count: unresolvedRows }
        ];

        clearEl(dom.filterChipsRow);
        chips.forEach(function (ch) {
            const active = state.filter.chip === ch.id;
            const btn = el('button', 'btn btn-sm font-mono bulk-filter-chip' + (active ? ' active' : ''));
            btn.type = 'button';
            btn.dataset.chip = ch.id;
            btn.appendChild(document.createTextNode(ch.label + ' '));
            const badge = el('span', 'badge rounded-pill ms-1 ' + (active ? 'bg-dark text-success' : 'bg-secondary bg-opacity-25 text-body-secondary'), ch.count.toLocaleString());
            btn.appendChild(badge);
            btn.addEventListener('click', function () {
                state.filter.chip = ch.id;
                state.page = 1;
                recomputeTableOnly();
            });
            dom.filterChipsRow.appendChild(btn);
        });

        // Active drill-down bar (from clicking a Shared IP or Class C)
        if (dom.activeDrilldownBar && dom.activeDrilldownText) {
            const parts = [];
            if (state.filter.ip) parts.push('IP = ' + state.filter.ip);
            if (state.filter.classC) parts.push('Class C = ' + state.filter.classC);
            if (state.filter.ipType) parts.push('IP Type = ' + state.filter.ipType);
            if (parts.length > 0) {
                dom.activeDrilldownText.textContent = 'Drill-down filter: ' + parts.join(' • ');
                dom.activeDrilldownBar.classList.remove('d-none');
            } else {
                dom.activeDrilldownBar.classList.add('d-none');
            }
        }
    }

    function ipTypeBadgeMeta(ipType) {
        switch (ipType) {
            case 'cdn_proxy':
                return { cls: 'bulk-badge-cdn', icon: 'bi-shield-shaded', text: 'CDN / Proxy' };
            case 'cloud':
                return { cls: 'bulk-badge-cloud', icon: 'bi-clouds-fill', text: 'Cloud' };
            case 'datacenter':
                return { cls: 'bulk-badge-dc', icon: 'bi-hdd-rack-fill', text: 'VPS / Datacenter' };
            case 'hosting_isp':
                return { cls: 'bulk-badge-hosting', icon: 'bi-server', text: 'Hosting / ISP' };
            case 'private_bogon':
                return { cls: 'bulk-badge-private', icon: 'bi-lock-fill', text: 'Private / Bogon' };
            default:
                return { cls: 'bulk-badge-unresolved', icon: 'bi-exclamation-triangle-fill', text: 'Unresolved' };
        }
    }

    function safeExternalUrl(host) {
        if (!host) return null;
        const clean = String(host).trim();
        if (!clean || /[\s"'<>\\]/.test(clean) || /^javascript:/i.test(clean) || /^data:/i.test(clean)) return null;
        if (clean.indexOf(':') !== -1 && /^[0-9a-f:]+$/i.test(clean)) {
            return 'http://[' + clean + ']';
        }
        return 'https://' + clean;
    }

    function renderSortHeaders() {
        if (!dom.resultsTableHead) return;
        const headers = dom.resultsTableHead.querySelectorAll('th[data-sort]');
        headers.forEach(function (th) {
            const key = th.getAttribute('data-sort');
            const icon = th.querySelector('.sort-indicator');
            if (!icon) return;
            if (state.sort.key === key) {
                th.classList.add('text-success');
                icon.className = 'bi sort-indicator ms-1 ' + (state.sort.dir === 'asc' ? 'bi-caret-up-fill' : 'bi-caret-down-fill');
            } else {
                th.classList.remove('text-success');
                icon.className = 'bi bi-arrow-down-up sort-indicator ms-1 opacity-50';
            }
        });
    }

    function renderResultsTable() {
        if (!dom.resultsTableBody) return;
        clearEl(dom.resultsTableBody);

        const totalFiltered = state.filteredIndices.length;
        const totalPages = Math.max(1, Math.ceil(totalFiltered / state.perPage));
        if (state.page > totalPages) state.page = totalPages;
        if (state.page < 1) state.page = 1;

        if (dom.resultsEmptyState) {
            dom.resultsEmptyState.classList.toggle('d-none', totalFiltered > 0);
        }

        const start = (state.page - 1) * state.perPage;
        const end = Math.min(totalFiltered, start + state.perPage);
        const frag = document.createDocumentFragment();

        for (let idx = start; idx < end; idx++) {
            const r = state.results[state.filteredIndices[idx]];
            const tr = document.createElement('tr');
            if (r.globalIsSharedIp) tr.classList.add('bulk-row-shared');
            if (r.status !== 'resolved') tr.classList.add('bulk-row-unresolved');

            // 1. Row #
            const tdNum = el('td', 'font-mono small text-body-secondary', String((r.seqIndex || 0) + 1));
            tr.appendChild(tdNum);

            // 2. Website / Host
            const tdHost = el('td');
            const hostWrap = el('div', 'd-flex align-items-center gap-1 flex-nowrap');
            const displayHost = r.host || r.input || '—';
            const hostSpan = el('span', 'fw-semibold font-mono text-body text-truncate d-inline-block', displayHost);
            hostSpan.style.maxWidth = '185px';
            hostSpan.title = displayHost;
            hostWrap.appendChild(hostSpan);

            const extUrl = r.valid ? safeExternalUrl(r.host) : null;
            if (extUrl) {
                const link = el('a', 'text-body-secondary small flex-shrink-0', '');
                link.href = extUrl;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.title = 'Open ' + displayHost;
                link.appendChild(el('i', 'bi bi-box-arrow-up-right'));
                hostWrap.appendChild(link);
            }
            tdHost.appendChild(hostWrap);

            if (r.input && r.host && r.input.toLowerCase() !== r.host.toLowerCase()) {
                const inDiv = el('div', 'small text-body-secondary font-mono text-truncate bulk-subtext', 'Input: ' + r.input);
                inDiv.title = r.input;
                tdHost.appendChild(inDiv);
            }
            if (r.cname) {
                const cnDiv = el('div', 'small text-info font-mono text-truncate bulk-subtext', 'CNAME: ' + r.cname);
                cnDiv.title = r.cname;
                tdHost.appendChild(cnDiv);
            }
            if (r.error) {
                const errDiv = el('div', 'small text-danger font-mono text-truncate bulk-subtext', r.error);
                errDiv.title = r.error;
                tdHost.appendChild(errDiv);
            }
            tr.appendChild(tdHost);

            // 3. IPv4
            const tdV4 = el('td', 'font-mono small text-nowrap');
            if (r.ipv4) {
                const v4Btn = el('button', 'btn btn-link btn-sm p-0 font-mono text-decoration-none text-body fw-semibold', r.ipv4);
                v4Btn.type = 'button';
                v4Btn.title = 'Filter by IP ' + r.ipv4;
                v4Btn.addEventListener('click', function () {
                    filterByIp(r.ipv4);
                });
                tdV4.appendChild(v4Btn);
                if (Array.isArray(r.ipv4_all) && r.ipv4_all.length > 1) {
                    const extra = el('span', 'badge bg-secondary bg-opacity-25 text-body-secondary ms-1', '+' + (r.ipv4_all.length - 1));
                    extra.title = r.ipv4_all.join(', ');
                    tdV4.appendChild(extra);
                }
            } else {
                tdV4.appendChild(el('span', 'text-body-secondary', '—'));
            }
            tr.appendChild(tdV4);

            // 4. IPv6
            const tdV6 = el('td', 'font-mono small');
            if (r.ipv6) {
                const v6Span = el('span', 'text-body-secondary d-inline-block text-truncate', r.ipv6);
                v6Span.style.maxWidth = '115px';
                v6Span.title = Array.isArray(r.ipv6_all) && r.ipv6_all.length ? r.ipv6_all.join(', ') : r.ipv6;
                tdV6.appendChild(v6Span);
            } else {
                tdV6.appendChild(el('span', 'text-body-secondary', '—'));
            }
            tr.appendChild(tdV6);

            // 5. IP Type Badge
            const tdType = el('td');
            const meta = ipTypeBadgeMeta(r.ip_type);
            const typeBadge = el('span', 'badge font-mono bulk-type-badge ' + meta.cls);
            typeBadge.appendChild(el('i', 'bi ' + meta.icon + ' me-1'));
            typeBadge.appendChild(document.createTextNode(r.ip_type_label || meta.text));
            tdType.appendChild(typeBadge);
            if (r.provider) {
                tdType.appendChild(el('div', 'small font-mono text-body-secondary mt-1 text-truncate bulk-subtext', r.provider));
            }
            tr.appendChild(tdType);

            // 6. Class / Version
            const tdClass = el('td', 'font-mono small text-nowrap');
            if (r.ip_version_label || r.ip_class) {
                if (r.ip_version_label) {
                    tdClass.appendChild(el('div', 'fw-semibold text-body', r.ip_version_label));
                }
                if (r.ip_class) {
                    tdClass.appendChild(el('div', 'small text-body-secondary bulk-subtext', r.ip_class));
                }
            } else {
                tdClass.appendChild(el('span', 'text-body-secondary', '—'));
            }
            tr.appendChild(tdClass);

            // 7. Class C (/24)
            const tdCc = el('td', 'font-mono small text-nowrap');
            if (r.class_c) {
                const ccBtn = el('button', 'btn btn-link btn-sm p-0 font-mono text-decoration-none text-body', r.class_c);
                ccBtn.type = 'button';
                ccBtn.title = 'Filter by Class C ' + r.class_c;
                ccBtn.addEventListener('click', function () {
                    filterByClassC(r.class_c);
                });
                tdCc.appendChild(ccBtn);
            } else if (r.ipv6_subnet_64) {
                const v6Net = el('span', 'text-body-secondary d-inline-block text-truncate', r.ipv6_subnet_64);
                v6Net.style.maxWidth = '110px';
                v6Net.title = 'IPv6 /64: ' + r.ipv6_subnet_64;
                tdCc.appendChild(v6Net);
            } else {
                tdCc.appendChild(el('span', 'text-body-secondary', '—'));
            }
            tr.appendChild(tdCc);

            // 8. ASN / ISP / Org
            const tdOrg = el('td', 'small');
            if (r.asn || r.org || r.country || r.rdns) {
                if (r.asn || r.country) {
                    const topLine = el('div', 'd-flex align-items-center gap-1 flex-wrap');
                    if (r.asn) {
                        topLine.appendChild(el('span', 'badge bg-secondary bg-opacity-25 text-body font-mono', r.asn));
                    }
                    if (r.country) {
                        topLine.appendChild(el('span', 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-mono', r.country));
                    }
                    tdOrg.appendChild(topLine);
                }
                if (r.org) {
                    const orgDiv = el('div', 'text-body-secondary font-mono text-truncate mt-1 bulk-subtext', r.org);
                    orgDiv.style.maxWidth = '165px';
                    orgDiv.title = r.org;
                    tdOrg.appendChild(orgDiv);
                }
                if (r.rdns) {
                    const rdnsDiv = el('div', 'text-body-secondary font-mono text-truncate bulk-subtext', 'PTR: ' + r.rdns);
                    rdnsDiv.style.maxWidth = '165px';
                    rdnsDiv.title = r.rdns;
                    tdOrg.appendChild(rdnsDiv);
                }
            } else {
                tdOrg.appendChild(el('span', 'text-body-secondary font-mono', '—'));
            }
            tr.appendChild(tdOrg);

            // 9. Same IP Status
            const tdSameIp = el('td', 'font-mono small text-nowrap');
            if (r.status !== 'resolved') {
                tdSameIp.appendChild(el('span', 'text-body-secondary', '—'));
            } else if (r.globalIsSharedIp) {
                const sharedBtn = el('button', 'btn btn-sm bulk-shared-pill font-mono', '');
                sharedBtn.type = 'button';
                sharedBtn.title = 'Click to filter all ' + r.globalSameIpCount + ' sites on this IP';
                sharedBtn.appendChild(el('i', 'bi bi-diagram-2-fill me-1'));
                sharedBtn.appendChild(document.createTextNode('Same IP (' + r.globalSameIpCount + ' sites)'));
                sharedBtn.addEventListener('click', function () {
                    const targetIp = r.primary_ip || r.ipv4 || r.ipv6;
                    if (targetIp) filterByIp(targetIp);
                });
                tdSameIp.appendChild(sharedBtn);
            } else {
                tdSameIp.appendChild(el('span', 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-mono', 'Unique'));
            }
            tr.appendChild(tdSameIp);

            // 10. Same Class C (Y/N)
            const tdSameCc = el('td', 'font-mono small text-center text-nowrap');
            if (r.status !== 'resolved' || !r.class_c) {
                tdSameCc.appendChild(el('span', 'text-body-secondary', '—'));
            } else if (r.globalIsSameClassC) {
                const ccFlagBtn = el('button', 'btn btn-sm bulk-cc-yes-pill font-mono', 'Y (' + r.globalSameClassCCount + ')');
                ccFlagBtn.type = 'button';
                ccFlagBtn.title = 'Filter Class C ' + r.class_c;
                ccFlagBtn.addEventListener('click', function () {
                    filterByClassC(r.class_c);
                });
                tdSameCc.appendChild(ccFlagBtn);
            } else {
                tdSameCc.appendChild(el('span', 'badge bg-secondary bg-opacity-10 text-body-secondary font-mono', 'N'));
            }
            tr.appendChild(tdSameCc);

            frag.appendChild(tr);
        }

        dom.resultsTableBody.appendChild(frag);
        renderPagination(totalFiltered, totalPages, start, end);
        renderSortHeaders();
    }

    function renderPagination(totalFiltered, totalPages, start, end) {
        if (dom.paginationSummary) {
            if (totalFiltered === 0) {
                dom.paginationSummary.textContent = '0 matching rows (of ' + state.results.length.toLocaleString() + ' total)';
            } else {
                dom.paginationSummary.textContent =
                    'Showing ' + (start + 1).toLocaleString() + '–' + end.toLocaleString() +
                    ' of ' + totalFiltered.toLocaleString() + ' matching' +
                    (totalFiltered !== state.results.length ? ' (' + state.results.length.toLocaleString() + ' total)' : '');
            }
        }
        renderPaginatorButtons(dom.paginationControls, state.page, totalPages, function (p) {
            state.page = p;
            renderResultsTable();
        });
    }

    function renderPaginatorButtons(container, currentPage, totalPages, onPageSelect) {
        if (!container) return;
        clearEl(container);
        if (totalPages <= 1) return;

        function makePageItem(label, targetPage, disabled, active) {
            const li = el('li', 'page-item' + (disabled ? ' disabled' : '') + (active ? ' active' : ''));
            const btn = el('button', 'page-link font-mono', label);
            btn.type = 'button';
            if (!disabled && !active) {
                btn.addEventListener('click', function () { onPageSelect(targetPage); });
            }
            li.appendChild(btn);
            return li;
        }

        container.appendChild(makePageItem('«', 1, currentPage === 1, false));
        container.appendChild(makePageItem('‹', currentPage - 1, currentPage === 1, false));

        const windowSize = 2;
        const startPage = Math.max(1, currentPage - windowSize);
        const endPage = Math.min(totalPages, currentPage + windowSize);
        for (let p = startPage; p <= endPage; p++) {
            container.appendChild(makePageItem(String(p), p, false, p === currentPage));
        }

        container.appendChild(makePageItem('›', currentPage + 1, currentPage === totalPages, false));
        container.appendChild(makePageItem('»', totalPages, currentPage === totalPages, false));
    }

    // -------------------------------------------------------------------------
    // Same-IP & Shared Class-C Cluster Report Tab
    // -------------------------------------------------------------------------
    function renderClustersTab() {
        if (!dom.clustersContainer) return;
        clearEl(dom.clustersContainer);

        const search = (state.clusterSearch || '').trim().toLowerCase();
        const showUnique = state.showUniqueClusters;
        const isByIp = state.clusterSubTab === 'by_ip';
        const rawList = isByIp ? state.clusters.byIp : state.clusters.byClassC;

        const filtered = rawList.filter(function (item) {
            if (!showUnique && item.count < 2) return false;
            if (!search) return true;
            const hay = isByIp
                ? [item.ip, item.class_c, item.provider, item.asn, item.org, item.country, item.hosts.join(' ')].filter(Boolean).join(' ').toLowerCase()
                : [item.class_c, item.ips.join(' '), item.providers.join(' '), item.hosts.join(' ')].filter(Boolean).join(' ').toLowerCase();
            return hay.indexOf(search) !== -1;
        });

        const total = filtered.length;
        const totalPages = Math.max(1, Math.ceil(total / state.clustersPerPage));
        if (state.clusterPage > totalPages) state.clusterPage = totalPages;
        if (state.clusterPage < 1) state.clusterPage = 1;

        if (total === 0) {
            const empty = el('div', 'empty-state-box rounded-4 p-5 text-center');
            empty.appendChild(el('i', 'bi bi-diagram-3 fs-1 text-success d-block mb-2'));
            empty.appendChild(el('h5', 'font-cyber fw-bold text-body mb-1',
                showUnique ? 'No Resolved Clusters Match Filter' : 'No Shared ' + (isByIp ? 'IP' : 'Class C (/24)') + ' Footprints Found'));
            empty.appendChild(el('p', 'text-body-secondary font-mono small mb-0',
                showUnique
                    ? 'Try clearing the cluster search filter.'
                    : 'All resolved websites currently reside on distinct ' + (isByIp ? 'IP addresses' : 'Class C /24 blocks') + '. Toggle "Show unique (1 site)" to inspect every group.'));
            dom.clustersContainer.appendChild(empty);
            if (dom.clusterPaginationSummary) dom.clusterPaginationSummary.textContent = '0 clusters shown';
            if (dom.clusterPaginationControls) clearEl(dom.clusterPaginationControls);
            return;
        }

        const start = (state.clusterPage - 1) * state.clustersPerPage;
        const end = Math.min(total, start + state.clustersPerPage);
        const frag = document.createDocumentFragment();

        for (let i = start; i < end; i++) {
            const grp = filtered[i];
            const card = el('div', 'card card-post p-3 p-md-4 mb-3 rounded-4');

            // Top row: Identifier + Count + Metadata + Actions
            const topRow = el('div', 'd-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3');
            const leftInfo = el('div');

            const titleRow = el('div', 'd-flex align-items-center flex-wrap gap-2 mb-1');
            const mainTitleBtn = el('button', 'btn btn-link p-0 font-mono fw-bold fs-5 text-decoration-none text-gradient text-start', isByIp ? grp.ip : grp.class_c);
            mainTitleBtn.type = 'button';
            mainTitleBtn.title = 'Click to filter results table';
            mainTitleBtn.addEventListener('click', function () {
                if (isByIp) filterByIp(grp.ip);
                else filterByClassC(grp.class_c);
            });
            titleRow.appendChild(mainTitleBtn);

            const countCls = grp.count > 1
                ? 'badge bg-warning bg-opacity-25 text-warning border border-warning border-opacity-50 font-mono'
                : 'badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-mono';
            titleRow.appendChild(el('span', countCls, grp.count.toLocaleString() + (grp.count === 1 ? ' Website' : ' Websites')));

            if (isByIp) {
                const meta = ipTypeBadgeMeta(grp.ip_type);
                const tBadge = el('span', 'badge font-mono bulk-type-badge ' + meta.cls, grp.ip_type_label || meta.text);
                titleRow.appendChild(tBadge);
                if (grp.provider) {
                    titleRow.appendChild(el('span', 'badge bg-secondary bg-opacity-25 text-body font-mono', grp.provider));
                }
            } else {
                titleRow.appendChild(el('span', 'badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 font-mono', grp.ipCount + ' distinct IP' + (grp.ipCount === 1 ? '' : 's')));
            }

            leftInfo.appendChild(titleRow);

            const subMeta = el('div', 'small font-mono text-body-secondary d-flex flex-wrap gap-3');
            if (isByIp) {
                if (grp.class_c) subMeta.appendChild(el('span', '', 'Class C: ' + grp.class_c));
                if (grp.asn) subMeta.appendChild(el('span', '', grp.asn));
                if (grp.org) subMeta.appendChild(el('span', '', grp.org));
                if (grp.country) subMeta.appendChild(el('span', '', 'Country: ' + grp.country));
            } else {
                subMeta.appendChild(el('span', '', 'IPs: ' + grp.ips.slice(0, 6).join(', ') + (grp.ips.length > 6 ? ' +' + (grp.ips.length - 6) + ' more' : '')));
                if (grp.providers.length) {
                    subMeta.appendChild(el('span', '', 'Providers: ' + grp.providers.slice(0, 4).join(', ')));
                }
            }
            leftInfo.appendChild(subMeta);

            const actions = el('div', 'd-flex flex-wrap gap-2 flex-shrink-0');
            const filterBtn = el('button', 'btn btn-sm btn-tactical font-mono');
            filterBtn.type = 'button';
            filterBtn.appendChild(el('i', 'bi bi-funnel me-1'));
            filterBtn.appendChild(document.createTextNode('Filter in Table'));
            filterBtn.addEventListener('click', function () {
                if (isByIp) filterByIp(grp.ip);
                else filterByClassC(grp.class_c);
            });
            actions.appendChild(filterBtn);

            const copyBtn = el('button', 'btn btn-sm btn-outline-secondary font-mono');
            copyBtn.type = 'button';
            copyBtn.appendChild(el('i', 'bi bi-clipboard me-1'));
            copyBtn.appendChild(document.createTextNode('Copy Domains'));
            copyBtn.addEventListener('click', function () {
                copyTextToClipboard(grp.hosts.join('\n'), 'Copied ' + grp.hosts.length + ' domains.');
            });
            actions.appendChild(copyBtn);

            topRow.appendChild(leftInfo);
            topRow.appendChild(actions);
            card.appendChild(topRow);

            // Domain chips list (expandable if > 12 domains)
            const key = (isByIp ? 'ip:' + grp.ip : 'cc:' + grp.class_c);
            const isExpanded = state.expandedClusters.has(key);
            const limit = isExpanded ? grp.hosts.length : Math.min(12, grp.hosts.length);

            const hostsWrap = el('div', 'd-flex flex-wrap gap-2 mt-3 pt-3 border-top border-secondary border-opacity-25');
            for (let h = 0; h < limit; h++) {
                const domain = grp.hosts[h];
                const pill = el('a', 'bulk-domain-pill font-mono small text-decoration-none');
                const ext = safeExternalUrl(domain);
                if (ext) {
                    pill.href = ext;
                    pill.target = '_blank';
                    pill.rel = 'noopener noreferrer';
                } else {
                    pill.href = '#';
                    pill.addEventListener('click', function (e) { e.preventDefault(); });
                }
                pill.appendChild(el('i', 'bi bi-globe2 me-1 text-success'));
                pill.appendChild(document.createTextNode(domain));
                hostsWrap.appendChild(pill);
            }

            if (grp.hosts.length > 12) {
                const toggleBtn = el('button', 'btn btn-sm btn-outline-success font-mono rounded-pill px-3',
                    isExpanded ? 'Show fewer' : 'Show all ' + grp.hosts.length.toLocaleString() + ' domains');
                toggleBtn.type = 'button';
                toggleBtn.addEventListener('click', function () {
                    if (state.expandedClusters.has(key)) state.expandedClusters.delete(key);
                    else state.expandedClusters.add(key);
                    renderClustersTab();
                });
                hostsWrap.appendChild(toggleBtn);
            }

            card.appendChild(hostsWrap);
            frag.appendChild(card);
        }

        dom.clustersContainer.appendChild(frag);
        if (dom.clusterPaginationSummary) {
            dom.clusterPaginationSummary.textContent =
                'Showing ' + (start + 1).toLocaleString() + '–' + end.toLocaleString() + ' of ' + total.toLocaleString() + ' clusters';
        }
        renderPaginatorButtons(dom.clusterPaginationControls, state.clusterPage, totalPages, function (p) {
            state.clusterPage = p;
            renderClustersTab();
        });
    }

    function filterByIp(ip) {
        state.filter.ip = ip;
        state.filter.classC = null;
        state.page = 1;
        switchMainTab('results');
        recomputeTableOnly();
    }

    function filterByClassC(cc) {
        state.filter.classC = cc;
        state.filter.ip = null;
        state.page = 1;
        switchMainTab('results');
        recomputeTableOnly();
    }

    function switchMainTab(tab) {
        state.activeTab = tab;
        const isResults = tab === 'results';
        if (dom.tabResultsBtn) dom.tabResultsBtn.classList.toggle('active', isResults);
        if (dom.tabClustersBtn) dom.tabClustersBtn.classList.toggle('active', !isResults);
        if (dom.resultsTabPane) dom.resultsTabPane.classList.toggle('d-none', !isResults);
        if (dom.clustersTabPane) dom.clustersTabPane.classList.toggle('d-none', isResults);
        if (!isResults) renderClustersTab();
    }

    function recomputeTableOnly() {
        applyFilterAndSort();
        renderFilterChips();
        renderResultsTable();
    }

    function recomputeAndRender() {
        recomputeGlobalClusters();
        renderKpis();
        recomputeTableOnly();
        if (state.activeTab === 'clusters') {
            renderClustersTab();
        }
    }

    // -------------------------------------------------------------------------
    // Exports: CSV (All / Shared / Clusters), JSON, Clipboard, TXT
    // -------------------------------------------------------------------------
    function csvEscape(val) {
        if (val === null || val === undefined) return '""';
        let s = String(val);
        // Guard against spreadsheet formula injection on untrusted strings
        if (/^[=+\-@\t\r]/.test(s)) {
            s = "'" + s;
        }
        return '"' + s.replace(/"/g, '""') + '"';
    }

    function downloadBlob(filename, content, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    }

    function buildRowsCsv(rows) {
        const header = [
            'Input', 'Host', 'Status', 'IPv4', 'IPv6', 'Primary IP', 'IP Type', 'Provider',
            'IP Version', 'IP Class', 'Class C (/24)', 'ASN', 'Org', 'Country', 'rDNS',
            'Same IP Count', 'Same IP Shared', 'Same Class C Count', 'Same Class C (Y/N)', 'Error'
        ];
        const lines = [header.map(csvEscape).join(',')];
        for (let i = 0; i < rows.length; i++) {
            const r = rows[i];
            const rowVals = [
                r.input || '',
                r.host || '',
                r.status || '',
                r.ipv4 || '',
                r.ipv6 || '',
                r.primary_ip || '',
                r.ip_type_label || r.ip_type || '',
                r.provider || '',
                r.ip_version_label || '',
                r.ip_class || '',
                r.class_c || '',
                r.asn || '',
                r.org || '',
                r.country || '',
                r.rdns || '',
                r.globalSameIpCount || 0,
                r.globalIsSharedIp ? 'Y' : 'N',
                r.globalSameClassCCount || 0,
                r.globalIsSameClassC ? 'Y' : 'N',
                r.error || ''
            ];
            lines.push(rowVals.map(csvEscape).join(','));
        }
        return '\uFEFF' + lines.join('\r\n');
    }

    function exportCsvAll() {
        if (!state.results.length) return showToast('No results to export.', 'warning');
        downloadBlob('bulk-ip-report-all.csv', buildRowsCsv(state.results), 'text/csv;charset=utf-8;');
        showToast('Exported all ' + state.results.length.toLocaleString() + ' rows to CSV.', 'success');
    }

    function exportCsvShared() {
        const shared = state.results.filter(function (r) { return r.globalIsSharedIp; }).sort(function (a, b) {
            const ipA = String(a.primary_ip || '');
            const ipB = String(b.primary_ip || '');
            return ipA.localeCompare(ipB) || String(a.host || '').localeCompare(String(b.host || ''));
        });
        if (!shared.length) return showToast('No shared IP rows found to export.', 'warning');
        downloadBlob('bulk-ip-report-shared-ips.csv', buildRowsCsv(shared), 'text/csv;charset=utf-8;');
        showToast('Exported ' + shared.length.toLocaleString() + ' shared-IP rows to CSV.', 'success');
    }

    function exportCsvClusters() {
        const lines = [[
            'Cluster Type', 'IP or Subnet', 'Websites Count', 'Distinct IPs',
            'IP Type', 'Provider', 'ASN', 'Org', 'Country', 'Domains'
        ].map(csvEscape).join(',')];

        let count = 0;
        state.clusters.byIp.forEach(function (g) {
            if (g.count > 1) {
                count++;
                lines.push([
                    'Same IP', g.ip, g.count, 1,
                    g.ip_type_label || g.ip_type, g.provider || '', g.asn || '', g.org || '', g.country || '',
                    g.hosts.join(' | ')
                ].map(csvEscape).join(','));
            }
        });
        state.clusters.byClassC.forEach(function (g) {
            if (g.count > 1) {
                count++;
                lines.push([
                    'Same Class C (/24)', g.class_c, g.count, g.ipCount,
                    '', g.providers.join(' | '), '', '', '',
                    g.hosts.join(' | ')
                ].map(csvEscape).join(','));
            }
        });

        if (count === 0) return showToast('No shared IP or Class C clusters to export.', 'warning');
        downloadBlob('bulk-ip-cluster-summary.csv', '\uFEFF' + lines.join('\r\n'), 'text/csv;charset=utf-8;');
        showToast('Exported ' + count + ' shared cluster summaries to CSV.', 'success');
    }

    function exportJson() {
        if (!state.results.length) return showToast('No results to export.', 'warning');
        const payload = {
            exported_at: new Date().toISOString(),
            summary: {
                total: state.results.length,
                resolved: state.clusters.resolvedCount,
                unresolved: state.clusters.unresolvedCount,
                invalid: state.clusters.invalidCount,
                unique_ips: state.clusters.uniqueIpCount,
                shared_ip_clusters: state.clusters.byIp.filter(function (g) { return g.count > 1; }).length,
                shared_class_c_clusters: state.clusters.byClassC.filter(function (g) { return g.count > 1; }).length,
                type_counts: state.clusters.typeCounts
            },
            clusters: {
                by_ip: state.clusters.byIp.filter(function (g) { return g.count > 1; }),
                by_class_c: state.clusters.byClassC.filter(function (g) { return g.count > 1; })
            },
            results: state.results
        };
        downloadBlob('bulk-ip-report.json', JSON.stringify(payload, null, 2), 'application/json;charset=utf-8;');
        showToast('Exported full JSON report.', 'success');
    }

    function copySharedReport() {
        const shared = state.clusters.byIp.filter(function (g) { return g.count > 1; });
        if (!shared.length) return showToast('No shared IP clusters found to copy.', 'warning');
        const lines = shared.map(function (g) {
            const meta = [g.ip_type_label, g.provider, g.asn, g.org].filter(Boolean).join(' | ');
            return g.ip + ' (' + g.count + ' sites' + (meta ? ' — ' + meta : '') + '):\n  ' + g.hosts.join(', ');
        });
        copyTextToClipboard(lines.join('\n\n'), 'Copied ' + shared.length + ' shared IP groups to clipboard.');
    }

    function copyFilteredDomainsTxt() {
        if (!state.filteredIndices.length) return showToast('No matching domains to copy.', 'warning');
        const hosts = state.filteredIndices.map(function (idx) {
            const r = state.results[idx];
            return r.host || r.input;
        }).filter(Boolean);
        copyTextToClipboard(hosts.join('\n'), 'Copied ' + hosts.length.toLocaleString() + ' domains to clipboard.');
    }

    // -------------------------------------------------------------------------
    // File Import & Drag-and-Drop
    // -------------------------------------------------------------------------
    function handleImportedFile(file) {
        if (!file) return;
        const name = (file.name || '').toLowerCase();
        if (!name.endsWith('.txt') && !name.endsWith('.csv') && !file.type.startsWith('text/')) {
            showStatusBanner('Please import a .txt or .csv file containing website URLs or domains.', 'warning');
            return;
        }
        if (file.size > 15 * 1024 * 1024) {
            showStatusBanner('File exceeds 15MB limit. Please split into smaller lists.', 'warning');
            return;
        }
        const reader = new FileReader();
        reader.onload = function (ev) {
            const text = String((ev && ev.target && ev.target.result) || '');
            if (dom.domainsInput) {
                dom.domainsInput.value = text;
                updateInputCounts();
                showStatusBanner('');
                showToast('Imported "' + file.name + '" (' + state.parsedSummary.uniqueHosts.length.toLocaleString() + ' unique hosts).', 'success');
            }
        };
        reader.onerror = function () {
            showStatusBanner('Could not read the selected file.', 'danger');
        };
        reader.readAsText(file);
    }

    // -------------------------------------------------------------------------
    // Event Wiring & Bootstrap
    // -------------------------------------------------------------------------
    function bindEvents() {
        let countDebounce = null;
        if (dom.domainsInput) {
            dom.domainsInput.addEventListener('input', function () {
                clearTimeout(countDebounce);
                countDebounce = setTimeout(updateInputCounts, 150);
            });
        }
        if (dom.deduplicateToggle) dom.deduplicateToggle.addEventListener('change', updateInputCounts);
        if (dom.batchSizeSelect) dom.batchSizeSelect.addEventListener('change', updateInputCounts);

        if (dom.loadSampleBtn) {
            dom.loadSampleBtn.addEventListener('click', function () {
                if (dom.domainsInput) {
                    dom.domainsInput.value = SAMPLE_DOMAINS.join('\n');
                    updateInputCounts();
                    showToast('Loaded ' + SAMPLE_DOMAINS.length + ' sample URLs (incl. Same-IP & IPv6 tests).', 'info');
                }
            });
        }
        if (dom.clearInputBtn) {
            dom.clearInputBtn.addEventListener('click', function () {
                if (dom.domainsInput) {
                    dom.domainsInput.value = '';
                    updateInputCounts();
                    showStatusBanner('');
                }
            });
        }

        if (dom.browseFileBtn && dom.fileInput) {
            dom.browseFileBtn.addEventListener('click', function () { dom.fileInput.click(); });
        }
        if (dom.fileInput) {
            dom.fileInput.addEventListener('change', function () {
                if (dom.fileInput.files && dom.fileInput.files[0]) {
                    handleImportedFile(dom.fileInput.files[0]);
                    dom.fileInput.value = '';
                }
            });
        }

        if (dom.fileDropZone) {
            ['dragenter', 'dragover'].forEach(function (evName) {
                dom.fileDropZone.addEventListener(evName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dom.fileDropZone.classList.add('bulk-drop-active');
                });
            });
            ['dragleave', 'drop'].forEach(function (evName) {
                dom.fileDropZone.addEventListener(evName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dom.fileDropZone.classList.remove('bulk-drop-active');
                });
            });
            dom.fileDropZone.addEventListener('drop', function (e) {
                if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
                    handleImportedFile(e.dataTransfer.files[0]);
                }
            });
        }

        if (dom.startScanBtn) dom.startScanBtn.addEventListener('click', startScan);
        if (dom.pauseScanBtn) dom.pauseScanBtn.addEventListener('click', pauseScan);
        if (dom.resumeScanBtn) dom.resumeScanBtn.addEventListener('click', resumeScan);
        if (dom.stopScanBtn) dom.stopScanBtn.addEventListener('click', stopScan);
        if (dom.retryFailedBtn) dom.retryFailedBtn.addEventListener('click', retryFailedBatches);
        if (dom.resetResultsBtn) dom.resetResultsBtn.addEventListener('click', resetResults);

        if (dom.tabResultsBtn) {
            dom.tabResultsBtn.addEventListener('click', function () { switchMainTab('results'); });
        }
        if (dom.tabClustersBtn) {
            dom.tabClustersBtn.addEventListener('click', function () { switchMainTab('clusters'); });
        }

        if (dom.clusterByIpBtn) {
            dom.clusterByIpBtn.addEventListener('click', function () {
                state.clusterSubTab = 'by_ip';
                state.clusterPage = 1;
                dom.clusterByIpBtn.classList.add('active');
                if (dom.clusterByClassCBtn) dom.clusterByClassCBtn.classList.remove('active');
                renderClustersTab();
            });
        }
        if (dom.clusterByClassCBtn) {
            dom.clusterByClassCBtn.addEventListener('click', function () {
                state.clusterSubTab = 'by_class_c';
                state.clusterPage = 1;
                dom.clusterByClassCBtn.classList.add('active');
                if (dom.clusterByIpBtn) dom.clusterByIpBtn.classList.remove('active');
                renderClustersTab();
            });
        }

        let searchDebounce = null;
        if (dom.tableSearchInput) {
            dom.tableSearchInput.addEventListener('input', function () {
                clearTimeout(searchDebounce);
                searchDebounce = setTimeout(function () {
                    state.filter.search = dom.tableSearchInput.value;
                    state.page = 1;
                    recomputeTableOnly();
                }, 120);
            });
        }
        if (dom.clearSearchBtn) {
            dom.clearSearchBtn.addEventListener('click', function () {
                if (dom.tableSearchInput) dom.tableSearchInput.value = '';
                state.filter.search = '';
                state.page = 1;
                recomputeTableOnly();
            });
        }
        if (dom.perPageSelect) {
            dom.perPageSelect.addEventListener('change', function () {
                state.perPage = Number(dom.perPageSelect.value) || 50;
                state.page = 1;
                renderResultsTable();
            });
        }
        if (dom.clearDrilldownBtn) {
            dom.clearDrilldownBtn.addEventListener('click', function () {
                state.filter.ip = null;
                state.filter.classC = null;
                state.filter.ipType = null;
                state.page = 1;
                recomputeTableOnly();
            });
        }

        if (dom.resultsTableHead) {
            dom.resultsTableHead.querySelectorAll('th[data-sort]').forEach(function (th) {
                th.addEventListener('click', function () {
                    const key = th.getAttribute('data-sort');
                    if (state.sort.key === key) {
                        state.sort.dir = state.sort.dir === 'asc' ? 'desc' : 'asc';
                    } else {
                        state.sort.key = key;
                        state.sort.dir = (key === 'globalSameIpCount' || key === 'globalSameClassCCount') ? 'desc' : 'asc';
                    }
                    state.page = 1;
                    recomputeTableOnly();
                });
            });
        }

        let clusterSearchDebounce = null;
        if (dom.clusterSearchInput) {
            dom.clusterSearchInput.addEventListener('input', function () {
                clearTimeout(clusterSearchDebounce);
                clusterSearchDebounce = setTimeout(function () {
                    state.clusterSearch = dom.clusterSearchInput.value;
                    state.clusterPage = 1;
                    renderClustersTab();
                }, 140);
            });
        }
        if (dom.showUniqueClustersToggle) {
            dom.showUniqueClustersToggle.addEventListener('change', function () {
                state.showUniqueClusters = dom.showUniqueClustersToggle.checked;
                state.clusterPage = 1;
                renderClustersTab();
            });
        }

        if (dom.exportCsvAllBtn) dom.exportCsvAllBtn.addEventListener('click', exportCsvAll);
        if (dom.exportCsvSharedBtn) dom.exportCsvSharedBtn.addEventListener('click', exportCsvShared);
        if (dom.exportCsvClustersBtn) dom.exportCsvClustersBtn.addEventListener('click', exportCsvClusters);
        if (dom.exportJsonBtn) dom.exportJsonBtn.addEventListener('click', exportJson);
        if (dom.copySharedBtn) dom.copySharedBtn.addEventListener('click', copySharedReport);
        if (dom.copyDomainsTxtBtn) dom.copyDomainsTxtBtn.addEventListener('click', copyFilteredDomainsTxt);
    }

    function applyQueryPrefill() {
        try {
            const params = new URLSearchParams(window.location.search);
            const domainsParam = params.get('domains');
            if (domainsParam && dom.domainsInput) {
                const lines = domainsParam.split(/[\r\n,;]+/).map(function (s) { return s.trim(); }).filter(Boolean);
                if (lines.length) {
                    dom.domainsInput.value = lines.join('\n');
                    updateInputCounts();
                    if (params.get('autorun') === '1') {
                        startScan();
                    }
                }
            }
        } catch (err) {
            // Ignore malformed query string
        }
    }

    function init() {
        cacheDom();
        if (!dom.domainsInput) return;
        bindEvents();
        updateInputCounts();
        applyQueryPrefill();
        syncControlButtons();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
