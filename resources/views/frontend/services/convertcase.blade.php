@extends('layouts.app')

@section('title', 'Convert Case - Online Text Case Converter | Shrawan Effects')
@section('meta_description', 'Free online Convert Case tool. Convert text into Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character, word, and line count.')
@section('canonical_url', \Illuminate\Support\Facades\Route::has('services.convertcase') ? route('services.convertcase') : url('/services/convertcase'))

@push('styles')
<style>
    /* ==========================================================================
       Convert Case Tool Styles (Cyberpunk HUD Dark Container matching screenshot)
       ========================================================================== */
    .cc-wrapper {
        max-width: 1140px;
        margin: 0 auto;
    }

    /* Main Black Textarea Container */
    .cc-editor-box {
        background-color: #000000;
        border: 1px solid #1a241b;
        border-radius: 8px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6), 0 0 1px rgba(0, 255, 102, 0.2);
        overflow: hidden;
        position: relative;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    .cc-editor-box:focus-within {
        border-color: rgba(0, 255, 102, 0.4);
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.7), 0 0 15px rgba(0, 255, 102, 0.15);
    }

    /* Textarea */
    .cc-textarea {
        width: 100%;
        min-height: 280px;
        background-color: transparent !important;
        color: #f1f5f9;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        font-size: 1.05rem;
        line-height: 1.6;
        padding: 1.25rem 1.5rem;
        border: none !important;
        outline: none !important;
        box-shadow: none !important;
        resize: vertical;
    }
    .cc-textarea::placeholder {
        color: #a3a3a3;
        font-size: 1.02rem;
    }

    /* Bottom Action Toolbar inside the container */
    .cc-toolbar {
        background-color: #0a0e0a;
        border-top: 1px solid #1c261e;
        padding: 0.65rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    /* Left Icon Buttons */
    .cc-tool-btn {
        background-color: #171c18;
        border: 1px solid #28342a;
        color: #d1d5db;
        border-radius: 6px;
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
        transition: all 0.2s ease;
        cursor: pointer;
        padding: 0;
        text-decoration: none;
    }
    .cc-tool-btn:hover {
        background-color: #232b24;
        color: #ffffff;
        border-color: #3e5042;
        transform: translateY(-1px);
    }
    .cc-tool-btn:active {
        transform: translateY(0);
    }
    .cc-tool-btn.btn-danger-hover:hover {
        background-color: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border-color: rgba(239, 68, 68, 0.4);
    }

    /* Right Counters */
    .cc-counters {
        color: #a1a1aa;
        font-size: 0.88rem;
        font-weight: 500;
        letter-spacing: 0.3px;
        user-select: none;
    }
    .cc-counters .separator {
        color: #52525b;
        margin: 0 0.4rem;
    }

    /* Case Buttons Row */
    .cc-buttons-row {
        margin-top: 1rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.6rem;
    }

    /* Transformation Pill Button */
    .cc-case-btn {
        background-color: #0b0f0c;
        border: 1px solid #242d26;
        color: #f1f5f9;
        font-size: 0.92rem;
        font-weight: 500;
        padding: 0.42rem 0.85rem 0.42rem 0.45rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
        cursor: pointer;
        user-select: none;
    }
    .cc-case-btn:hover {
        background-color: #161e18;
        border-color: #3b4b3f;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
    .cc-case-btn:active {
        transform: translateY(0);
    }

    /* Prefix Badges with exact colors from reference */
    .cc-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.72rem;
        font-weight: 700;
        font-family: monospace;
        letter-spacing: -0.5px;
        padding: 0.15rem 0.45rem;
        border-radius: 4px;
        margin-right: 0.55rem;
        min-width: 24px;
        line-height: 1.2;
    }

    /* Specific Badge Backgrounds */
    .badge-sc { background-color: #9a3412; color: #ffffff; } /* Sentence case - rust/orange */
    .badge-lc { background-color: #15803d; color: #ffffff; } /* lower case - green */
    .badge-uc { background-color: #0284c7; color: #ffffff; } /* UPPER CASE - cyan/blue */
    .badge-cc { background-color: #7c3aed; color: #ffffff; } /* Capitalized Case - purple */
    .badge-ac { background-color: #ca8a04; color: #ffffff; } /* aLtErNaTiNg cAsE - gold/yellow */
    .badge-tc { background-color: #0d9488; color: #ffffff; } /* Title Case - teal */
    .badge-ic { background-color: #c026d3; color: #ffffff; } /* InVeRsE CaSe - magenta */

    /* Buy me a coffee Button */
    .cc-coffee-btn {
        background-color: #ea580c;
        border: 1px solid #f97316;
        color: #ffffff !important;
        font-size: 0.92rem;
        font-weight: 600;
        padding: 0.42rem 1rem;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        text-decoration: none;
        transition: all 0.2s ease;
        margin-left: auto;
    }
    .cc-coffee-btn:hover {
        background-color: #c2410c;
        border-color: #ea580c;
        color: #ffffff !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.45);
    }
    .cc-coffee-btn i {
        font-size: 1.05rem;
    }

    /* Feedback Toast */
    .cc-toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: #0f1712;
        border: 1px solid #00ff66;
        color: #ffffff;
        padding: 0.85rem 1.4rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7), 0 0 15px rgba(0, 255, 102, 0.3);
        z-index: 99999;
        display: none;
        align-items: center;
        gap: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        animation: ccToastFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes ccToastFadeIn {
        from { opacity: 0; transform: translateY(12px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    /* Settings Dropdown Panel */
    .cc-settings-dropdown {
        background: #0d130e;
        border: 1px solid #28372b;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.7);
        min-width: 280px;
    }

    /* SEO Article Cards */
    .cc-seo-card {
        background: rgba(9, 15, 10, 0.75);
        border: 1px solid rgba(0, 255, 102, 0.15);
        border-radius: 14px;
        padding: 1.75rem;
        margin-top: 1.75rem;
    }
</style>
@endpush

@section('content')
<div class="py-4 hero-banner">
    <div class="container cc-wrapper">
        <!-- Breadcrumb & Header -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 font-mono" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('services.index') }}" class="text-decoration-none">Services</a></li>
                <li class="breadcrumb-item active" aria-current="page">Convert Case</li>
            </ol>
        </nav>

        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span class="telemetry-tag"><i class="bi bi-type"></i> ONLINE UTILITY // TEXT CASE CONVERTER</span>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-mono small">
                        <span class="cyber-beacon me-1"></span> 100% CLIENT-SIDE & SECURE
                    </span>
                </div>
                <h1 class="fw-bold mb-1 display-5 font-cyber">
                    Convert <span class="text-gradient">Case</span>
                </h1>
                <p class="text-body-secondary mb-0 small">
                    Instantly transform and format your text between sentence case, lower case, upper case, capitalized case, alternating case, title case, and inverse case.
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 mt-2 mt-md-0">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill font-mono" id="btnUndo" title="Undo last change (Ctrl+Z)" disabled>
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Undo
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill font-mono" id="btnRedo" title="Redo last change (Ctrl+Y)" disabled>
                    <i class="bi bi-arrow-clockwise me-1"></i> Redo
                </button>
            </div>
        </div>

        <!-- ==========================================================================
             THE TOOL CONTAINER (matching media_1795720420539_eab68368.png exactly)
             ========================================================================== -->
        <div class="cc-editor-box">
            <!-- Text Input Area -->
            <textarea
                id="ccInput"
                class="cc-textarea"
                placeholder="Type or paste your content here"
                spellcheck="true"
                rows="9"
                aria-label="Content to convert"
            ></textarea>

            <!-- Bottom Floating Toolbar -->
            <div class="cc-toolbar">
                <!-- Left Action Icon Buttons -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Copy to Clipboard -->
                    <button type="button" class="cc-tool-btn" id="btnCopy" title="Copy to Clipboard (Ctrl+C)" aria-label="Copy">
                        <i class="bi bi-copy"></i>
                    </button>
                    <!-- Share -->
                    <button type="button" class="cc-tool-btn" id="btnShare" title="Share or Copy Link" aria-label="Share">
                        <i class="bi bi-share"></i>
                    </button>
                    <!-- Download as .txt -->
                    <button type="button" class="cc-tool-btn" id="btnDownload" title="Download text as .txt" aria-label="Download">
                        <i class="bi bi-download"></i>
                    </button>
                    <!-- Clear Content -->
                    <button type="button" class="cc-tool-btn btn-danger-hover" id="btnClear" title="Clear Content" aria-label="Clear">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <!-- Settings Dropdown -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="cc-tool-btn dropdown-toggle-no-caret" id="btnSettings" data-bs-toggle="dropdown" aria-expanded="false" title="Settings & Options" aria-label="Settings">
                            <i class="bi bi-gear"></i>
                        </button>
                        <div class="dropdown-menu cc-settings-dropdown dropdown-menu-dark">
                            <h6 class="dropdown-header text-uppercase text-secondary font-mono px-0 pt-0">Options</h6>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="optTrimWhitespace" checked>
                                <label class="form-check-label small text-body" for="optTrimWhitespace">Auto-trim extra spaces</label>
                            </div>
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="optPreserveAcronyms">
                                <label class="form-check-label small text-body" for="optPreserveAcronyms">Preserve recognized acronyms (NASA, USA, AI)</label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="optExcludeSpacesCount">
                                <label class="form-check-label small text-body" for="optExcludeSpacesCount">Character count without spaces</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Live Character / Word / Line Counters -->
                <div class="cc-counters font-mono" id="ccCounters">
                    Character Count: <span id="countChars">0</span>
                    <span class="separator">|</span>
                    Word Count: <span id="countWords">0</span>
                    <span class="separator">|</span>
                    Line Count: <span id="countLines">0</span>
                </div>
            </div>
        </div>

        <!-- Buttons Row (Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, InVeRsE CaSe, Buy me a coffee) -->
        <div class="cc-buttons-row">
            <!-- 1. Sentence case -->
            <button type="button" class="cc-case-btn" id="btnSentenceCase" title="Capitalize the first letter of each sentence">
                <span class="cc-badge badge-sc">Sc</span>
                <span>Sentence case</span>
            </button>

            <!-- 2. lower case -->
            <button type="button" class="cc-case-btn" id="btnLowerCase" title="Convert all letters to lower case">
                <span class="cc-badge badge-lc">lc</span>
                <span>lower case</span>
            </button>

            <!-- 3. UPPER CASE -->
            <button type="button" class="cc-case-btn" id="btnUpperCase" title="Convert all letters to UPPER CASE">
                <span class="cc-badge badge-uc">UC</span>
                <span>UPPER CASE</span>
            </button>

            <!-- 4. Capitalized Case -->
            <button type="button" class="cc-case-btn" id="btnCapitalizedCase" title="Capitalize the first letter of each word">
                <span class="cc-badge badge-cc">CC</span>
                <span>Capitalized Case</span>
            </button>

            <!-- 5. aLtErNaTiNg cAsE -->
            <button type="button" class="cc-case-btn" id="btnAlternatingCase" title="Alternate lower and uppercase letters">
                <span class="cc-badge badge-ac">aC</span>
                <span>aLtErNaTiNg cAsE</span>
            </button>

            <!-- 6. Title Case -->
            <button type="button" class="cc-case-btn" id="btnTitleCase" title="Capitalize principal words conforming to editorial title casing">
                <span class="cc-badge badge-tc">TC</span>
                <span>Title Case</span>
            </button>

            <!-- 7. InVeRsE CaSe -->
            <button type="button" class="cc-case-btn" id="btnInverseCase" title="Invert the case of each character">
                <span class="cc-badge badge-ic">iC</span>
                <span>InVeRsE CaSe</span>
            </button>

            <!-- 8. Buy me a coffee -->
            <a href="https://buymeacoffee.com/shrawaneffects" target="_blank" rel="noopener noreferrer" class="cc-coffee-btn" title="Support the developer">
                <span>Buy me a</span>
                <i class="bi bi-cup-hot-fill"></i>
            </a>
        </div>

        <!-- ==========================================================================
             COMPREHENSIVE SEO CONTENT & USAGE GUIDE
             ========================================================================== -->
        <div class="cc-seo-card mt-5">
            <h2 class="fw-bold fs-4 text-gradient mb-3">About Shrawan Effects Convert Case</h2>
            <p class="text-body-secondary leading-relaxed">
                Welcome to the official <strong>Convert Case</strong> tool by Shrawan Effects. This utility is built to help writers, coders, students, and SEO specialists quickly reformat plain text, articles, blog post titles, code comments, and lists into any case formatting style with a single click.
            </p>

            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-success mb-2"><span class="badge bg-danger-subtle text-danger me-1">Sc</span> Sentence Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Capitalizes the first letter of each sentence while turning the rest into lowercase. It detects sentence terminators such as periods (.), exclamation marks (!), question marks (?), and line breaks.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-success mb-2"><span class="badge bg-success-subtle text-success me-1">lc</span> Lower Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Converts every single character in your text into lowercase letters. Perfect for normalizing messy inputs, URLs, emails, or coding variables.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-info mb-2"><span class="badge bg-info-subtle text-info me-1">UC</span> Upper Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Converts all alphabetical letters into capital uppercase letters. Ideal for headers, strong call-to-actions, or acronym emphasizing.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-primary mb-2"><span class="badge bg-primary-subtle text-primary me-1">CC</span> Capitalized Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Capitalizes the first letter of every single word in your content without exception, converting subsequent characters in the word to lowercase.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-warning mb-2"><span class="badge bg-warning-subtle text-warning me-1">aC</span> Alternating Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Alternates the case of characters between lower and upper case (e.g. <em>aLtErNaTiNg cAsE</em>). Popular for social media memes and expressive messaging.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-teal mb-2"><span class="badge bg-teal-subtle text-success me-1">TC</span> Title Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Standard editorial headline capitalization. Capitalizes major words while keeping minor words (such as 'and', 'in', 'of', 'the', 'to', 'for') lowercase unless they begin or end the headline.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-danger mb-2"><span class="badge bg-secondary-subtle text-light me-1">iC</span> InVeRsE Case</h3>
                        <p class="small text-body-secondary mb-0">
                            Inverts the capitalization of each character: any lowercase letter becomes uppercase, and any uppercase letter becomes lowercase. Extremely useful when you accidentally left Caps Lock turned on!
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 rounded-3 bg-body-tertiary border border-secondary border-opacity-25 h-100">
                        <h3 class="fs-6 fw-bold text-success mb-2"><i class="bi bi-shield-check me-1"></i> Privacy & Zero Server Storage</h3>
                        <p class="small text-body-secondary mb-0">
                            100% of text processing takes place locally inside your browser via high-speed JavaScript. No sensitive text, passwords, or documents are ever transferred or saved to our web server.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Floating Toast Notification -->
<div id="ccToast" class="cc-toast" role="alert" aria-live="assertive" aria-atomic="true">
    <i class="bi bi-check2-circle text-success fs-5"></i>
    <span id="ccToastMessage">Copied to clipboard!</span>
</div>

@push('scripts')
<script>
(function() {
    'use strict';

    const input = document.getElementById('ccInput');
    const countChars = document.getElementById('countChars');
    const countWords = document.getElementById('countWords');
    const countLines = document.getElementById('countLines');
    const btnUndo = document.getElementById('btnUndo');
    const btnRedo = document.getElementById('btnRedo');

    const optTrimWhitespace = document.getElementById('optTrimWhitespace');
    const optPreserveAcronyms = document.getElementById('optPreserveAcronyms');
    const optExcludeSpacesCount = document.getElementById('optExcludeSpacesCount');

    const toast = document.getElementById('ccToast');
    const toastMsg = document.getElementById('ccToastMessage');
    let toastTimeout = null;

    // Undo / Redo history stacks
    const history = [];
    let historyIndex = -1;
    const MAX_HISTORY = 40;

    function showToast(message, isSuccess = true) {
        if (!toast) return;
        toastMsg.textContent = message;
        toast.style.display = 'flex';
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 2200);
    }

    function recordState(value) {
        // Truncate redo states
        if (historyIndex < history.length - 1) {
            history.splice(historyIndex + 1);
        }
        if (history.length === 0 || history[history.length - 1] !== value) {
            history.push(value);
            if (history.length > MAX_HISTORY) history.shift();
            historyIndex = history.length - 1;
        }
        updateUndoRedoButtons();
    }

    function updateUndoRedoButtons() {
        if (btnUndo) btnUndo.disabled = (historyIndex <= 0);
        if (btnRedo) btnRedo.disabled = (historyIndex >= history.length - 1);
    }

    function updateCounters() {
        const text = input.value;
        // Characters
        if (optExcludeSpacesCount && optExcludeSpacesCount.checked) {
            countChars.textContent = text.replace(/\s/g, '').length;
        } else {
            countChars.textContent = text.length;
        }

        // Words
        const words = text.trim().split(/\s+/).filter(Boolean);
        countWords.textContent = words.length;

        // Lines
        if (text.length === 0) {
            countLines.textContent = '0';
        } else {
            countLines.textContent = text.split(/\r\n|\r|\n/).length;
        }
    }

    function applyTransformation(transformFn, label) {
        const original = input.value;
        if (!original) return;

        let result = transformFn(original);
        if (optTrimWhitespace && optTrimWhitespace.checked) {
            // Normalise excessive trailing spaces on lines
            result = result.replace(/[ \t]+$/gm, '');
        }

        input.value = result;
        recordState(result);
        updateCounters();
        showToast(label + ' applied');
        input.focus();
    }

    // 1. Sentence Case: Capitalizes first letter of each sentence
    function toSentenceCase(text) {
        const lower = text.toLowerCase();
        // Capitalize after start of string or after period, exclamation, question mark followed by whitespace
        return lower.replace(/(^\s*|[.!?]\s+)(\p{L})/gu, function(match, prefix, char) {
            return prefix + char.toUpperCase();
        });
    }

    // 2. Lower Case
    function toLowerCase(text) {
        return text.toLowerCase();
    }

    // 3. Upper Case
    function toUpperCase(text) {
        return text.toUpperCase();
    }

    // 4. Capitalized Case: Capitalize First Letter Of Every Word
    function toCapitalizedCase(text) {
        return text.toLowerCase().replace(/(^|[^\p{L}])(\p{L})/gu, function(match, boundary, char) {
            return boundary + char.toUpperCase();
        });
    }

    // 5. Alternating Case: aLtErNaTiNg cAsE
    function toAlternatingCase(text) {
        let isUpper = false;
        let result = '';
        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            if (/\p{L}/u.test(char)) {
                result += isUpper ? char.toUpperCase() : char.toLowerCase();
                isUpper = !isUpper;
            } else {
                result += char;
            }
        }
        return result;
    }

    // 6. Title Case: Standard editorial headline casing
    const minorWords = new Set([
        'a', 'an', 'and', 'as', 'at', 'but', 'by', 'for', 'from', 'in',
        'into', 'nor', 'of', 'off', 'on', 'onto', 'or', 'so', 'the',
        'to', 'up', 'with', 'yet'
    ]);

    function toTitleCase(text) {
        return text.replace(/[A-Za-z0-9]+(?:'[A-Za-z0-9]+)?/g, function(word, index, full) {
            const lower = word.toLowerCase();
            const isFirst = (index === 0 || full.slice(0, index).match(/[\n\r.!?]\s*$/));
            const isLast = (index + word.length >= full.length || full.slice(index + word.length).match(/^\s*[\n\r.!?]/));

            if (!isFirst && !isLast && minorWords.has(lower)) {
                return lower;
            }
            return lower.charAt(0).toUpperCase() + lower.slice(1);
        });
    }

    // 7. Inverse Case: Inverts case of each letter
    function toInverseCase(text) {
        let result = '';
        for (let i = 0; i < text.length; i++) {
            const c = text[i];
            if (c === c.toUpperCase() && c !== c.toLowerCase()) {
                result += c.toLowerCase();
            } else if (c === c.toLowerCase() && c !== c.toUpperCase()) {
                result += c.toUpperCase();
            } else {
                result += c;
            }
        }
        return result;
    }

    // Button Event Listeners
    document.getElementById('btnSentenceCase').addEventListener('click', () => applyTransformation(toSentenceCase, 'Sentence case'));
    document.getElementById('btnLowerCase').addEventListener('click', () => applyTransformation(toLowerCase, 'Lower case'));
    document.getElementById('btnUpperCase').addEventListener('click', () => applyTransformation(toUpperCase, 'Upper case'));
    document.getElementById('btnCapitalizedCase').addEventListener('click', () => applyTransformation(toCapitalizedCase, 'Capitalized case'));
    document.getElementById('btnAlternatingCase').addEventListener('click', () => applyTransformation(toAlternatingCase, 'Alternating case'));
    document.getElementById('btnTitleCase').addEventListener('click', () => applyTransformation(toTitleCase, 'Title case'));
    document.getElementById('btnInverseCase').addEventListener('click', () => applyTransformation(toInverseCase, 'Inverse case'));

    // Copy Button
    document.getElementById('btnCopy').addEventListener('click', async function() {
        if (!input.value) {
            showToast('Nothing to copy!');
            return;
        }
        try {
            await navigator.clipboard.writeText(input.value);
            showToast('Copied to clipboard!');
        } catch (e) {
            input.select();
            document.execCommand('copy');
            showToast('Copied to clipboard!');
        }
    });

    // Share Button
    document.getElementById('btnShare').addEventListener('click', async function() {
        const shareData = {
            title: 'Convert Case - Shrawan Effects',
            text: input.value.slice(0, 140),
            url: window.location.href
        };
        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
            try {
                await navigator.share(shareData);
                return;
            } catch (err) {}
        }
        // Fallback: Copy link
        try {
            await navigator.clipboard.writeText(window.location.href);
            showToast('Tool link copied to clipboard!');
        } catch (err) {
            showToast('Tool link: ' + window.location.href);
        }
    });

    // Download Button (.txt)
    document.getElementById('btnDownload').addEventListener('click', function() {
        const content = input.value;
        if (!content) {
            showToast('Nothing to download!');
            return;
        }
        const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'convertcase-' + Date.now() + '.txt';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        showToast('Text file downloaded!');
    });

    // Clear Button
    document.getElementById('btnClear').addEventListener('click', function() {
        if (!input.value) return;
        if (confirm('Are you sure you want to clear all text?')) {
            input.value = '';
            recordState('');
            updateCounters();
            showToast('Content cleared');
        }
    });

    // Undo / Redo
    btnUndo.addEventListener('click', function() {
        if (historyIndex > 0) {
            historyIndex--;
            input.value = history[historyIndex];
            updateUndoRedoButtons();
            updateCounters();
            showToast('Undo');
        }
    });

    btnRedo.addEventListener('click', function() {
        if (historyIndex < history.length - 1) {
            historyIndex++;
            input.value = history[historyIndex];
            updateUndoRedoButtons();
            updateCounters();
            showToast('Redo');
        }
    });

    // Input events
    let debounceTimer;
    input.addEventListener('input', function() {
        updateCounters();
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            recordState(input.value);
        }, 400);
    });

    // Options changes
    [optTrimWhitespace, optPreserveAcronyms, optExcludeSpacesCount].forEach(opt => {
        if (opt) {
            opt.addEventListener('change', updateCounters);
        }
    });

    // Keyboard shortcuts: Ctrl+Z / Ctrl+Y
    input.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
            e.preventDefault();
            btnUndo.click();
        } else if (((e.ctrlKey || e.metaKey) && e.key === 'y') || ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'z')) {
            e.preventDefault();
            btnRedo.click();
        }
    });

    // Initial state record
    recordState(input.value);
    updateCounters();
})();
</script>
@endpush
@endsection
