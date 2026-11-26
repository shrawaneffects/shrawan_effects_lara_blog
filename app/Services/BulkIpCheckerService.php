<?php

namespace App\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;

class BulkIpCheckerService
{
    public const MAX_BATCH_SIZE = 50;
    public const RECOMMENDED_BATCH_SIZE = 30;
    public const RECOMMENDED_CONCURRENCY = 3;

    private const DNS_CACHE_TTL_SECONDS = 600;
    private const IP_META_CACHE_TTL_SECONDS = 86400;
    private const ASN_CACHE_TTL_SECONDS = 604800;

    private const DOH_CONNECT_TIMEOUT = 2;
    private const DOH_REQUEST_TIMEOUT = 4;

    public const TYPE_CDN_PROXY = 'cdn_proxy';
    public const TYPE_CLOUD = 'cloud';
    public const TYPE_DATACENTER = 'datacenter';
    public const TYPE_HOSTING_ISP = 'hosting_isp';
    public const TYPE_PRIVATE_BOGON = 'private_bogon';
    public const TYPE_UNRESOLVED = 'unresolved';

    public const TYPE_LABELS = [
        self::TYPE_CDN_PROXY => 'CDN / Reverse Proxy',
        self::TYPE_CLOUD => 'Cloud Hyperscaler',
        self::TYPE_DATACENTER => 'VPS / Datacenter',
        self::TYPE_HOSTING_ISP => 'Hosting / ISP',
        self::TYPE_PRIVATE_BOGON => 'Private / Bogon',
        self::TYPE_UNRESOLVED => 'Unresolved',
    ];

    /**
     * Special-use and non-routable IPv4 CIDR blocks.
     *
     * @var array<int, array{cidr: string, scope: string, provider: string}>
     */
    private const IPV4_SPECIAL_RANGES = [
        ['cidr' => '10.0.0.0/8', 'scope' => 'Private (RFC1918)', 'provider' => 'Private RFC1918'],
        ['cidr' => '172.16.0.0/12', 'scope' => 'Private (RFC1918)', 'provider' => 'Private RFC1918'],
        ['cidr' => '192.168.0.0/16', 'scope' => 'Private (RFC1918)', 'provider' => 'Private RFC1918'],
        ['cidr' => '127.0.0.0/8', 'scope' => 'Loopback (RFC1122)', 'provider' => 'Loopback'],
        ['cidr' => '100.64.0.0/10', 'scope' => 'CGNAT (RFC6598)', 'provider' => 'Shared Address Space (CGNAT)'],
        ['cidr' => '169.254.0.0/16', 'scope' => 'Link-Local (RFC3927)', 'provider' => 'Link-Local'],
        ['cidr' => '0.0.0.0/8', 'scope' => 'Reserved (This Network)', 'provider' => 'IANA Reserved'],
        ['cidr' => '192.0.0.0/24', 'scope' => 'Reserved (IETF Protocol)', 'provider' => 'IANA Reserved'],
        ['cidr' => '192.0.2.0/24', 'scope' => 'Reserved (TEST-NET-1)', 'provider' => 'Documentation Range'],
        ['cidr' => '198.18.0.0/15', 'scope' => 'Reserved (Benchmarking)', 'provider' => 'Benchmarking Range'],
        ['cidr' => '198.51.100.0/24', 'scope' => 'Reserved (TEST-NET-2)', 'provider' => 'Documentation Range'],
        ['cidr' => '203.0.113.0/24', 'scope' => 'Reserved (TEST-NET-3)', 'provider' => 'Documentation Range'],
        ['cidr' => '224.0.0.0/4', 'scope' => 'Multicast (Class D)', 'provider' => 'Multicast'],
        ['cidr' => '240.0.0.0/4', 'scope' => 'Reserved (Class E)', 'provider' => 'Class E Reserved'],
    ];

    /**
     * Special-use IPv6 CIDR blocks.
     *
     * @var array<int, array{cidr: string, scope: string, provider: string}>
     */
    private const IPV6_SPECIAL_RANGES = [
        ['cidr' => '::1/128', 'scope' => 'IPv6 Loopback', 'provider' => 'Loopback'],
        ['cidr' => '::/128', 'scope' => 'IPv6 Unspecified', 'provider' => 'IANA Reserved'],
        ['cidr' => 'fc00::/7', 'scope' => 'IPv6 Unique Local (ULA)', 'provider' => 'Private ULA'],
        ['cidr' => 'fe80::/10', 'scope' => 'IPv6 Link-Local', 'provider' => 'Link-Local'],
        ['cidr' => '2001:db8::/32', 'scope' => 'IPv6 Documentation', 'provider' => 'Documentation Range'],
        ['cidr' => 'ff00::/8', 'scope' => 'IPv6 Multicast', 'provider' => 'Multicast'],
    ];

    /**
     * Fast local CIDR lookup table for major CDNs, Cloud Hyperscalers, and Datacenters.
     *
     * @var array<int, array{type: string, provider: string, cidrs: array<int, string>}>
     */
    private const KNOWN_CIDR_PROVIDERS = [
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => 'Cloudflare',
            'cidrs' => [
                '173.245.48.0/20', '103.21.244.0/22', '103.22.200.0/22', '103.31.4.0/22',
                '141.101.64.0/18', '108.162.192.0/18', '190.93.240.0/20', '188.114.96.0/20',
                '197.234.240.0/22', '198.41.128.0/17', '162.158.0.0/15', '104.16.0.0/13',
                '104.24.0.0/14', '172.64.0.0/13', '131.0.72.0/22',
                '2400:cb00::/32', '2606:4700::/32', '2803:f800::/32',
                '2405:b500::/32', '2405:8100::/32', '2a06:98c0::/29', '2c0f:f248::/32',
            ],
        ],
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => 'Fastly',
            'cidrs' => [
                '23.235.32.0/20', '43.249.72.0/22', '103.244.50.0/24', '103.245.222.0/23',
                '103.245.224.0/24', '104.156.80.0/20', '140.248.64.0/18', '140.248.128.0/17',
                '146.75.0.0/17', '151.101.0.0/16', '157.52.64.0/18', '167.82.0.0/17',
                '167.82.128.0/20', '167.82.160.0/20', '167.82.224.0/20', '172.111.64.0/18',
                '185.31.16.0/22', '199.27.72.0/21', '199.232.0.0/16',
                '2a04:4e40::/32', '2a04:4e42::/32',
            ],
        ],
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => '20i / StackCDN',
            'cidrs' => ['185.151.28.0/22', '2a07:7800::/32'],
        ],
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => 'Sucuri',
            'cidrs' => ['192.88.134.0/23', '185.93.228.0/22', '66.248.200.0/22', '208.109.0.0/22', '2a02:fe80::/29'],
        ],
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => 'Amazon CloudFront',
            'cidrs' => ['13.32.0.0/15', '13.224.0.0/14', '99.84.0.0/16', '108.156.0.0/14', '143.204.0.0/16'],
        ],
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => 'Akamai',
            'cidrs' => ['23.192.0.0/11', '23.32.0.0/11', '104.64.0.0/10'],
        ],
        [
            'type' => self::TYPE_CDN_PROXY,
            'provider' => 'Vercel Edge',
            'cidrs' => ['76.76.21.0/24', '76.223.126.88/29'],
        ],
        [
            'type' => self::TYPE_DATACENTER,
            'provider' => 'DigitalOcean',
            'cidrs' => [
                '104.131.0.0/16', '134.209.0.0/16', '138.197.0.0/16', '142.93.0.0/16',
                '157.230.0.0/16', '159.65.0.0/16', '165.22.0.0/16', '167.71.0.0/16',
                '167.99.0.0/16', '178.128.0.0/16', '206.189.0.0/16', '2604:a880::/32',
            ],
        ],
        [
            'type' => self::TYPE_CLOUD,
            'provider' => 'Google Cloud',
            'cidrs' => ['34.0.0.0/9', '35.184.0.0/13', '35.192.0.0/12', '35.224.0.0/12', '142.250.0.0/15', '2404:6800::/32', '2607:f8b0::/32'],
        ],
        [
            'type' => self::TYPE_CLOUD,
            'provider' => 'Amazon AWS',
            'cidrs' => ['3.0.0.0/9', '18.128.0.0/9', '52.0.0.0/11', '54.144.0.0/12'],
        ],
        [
            'type' => self::TYPE_CLOUD,
            'provider' => 'Microsoft Azure',
            'cidrs' => ['13.64.0.0/11', '20.0.0.0/11', '40.64.0.0/10', '52.160.0.0/11'],
        ],
    ];

    /**
     * ASN number => classification mapping.
     *
     * @var array<int, array{type: string, provider: string}>
     */
    private const KNOWN_ASNS = [
        13335 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Cloudflare'],
        209242 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Cloudflare'],
        54113 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Fastly'],
        20940 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        16625 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        35994 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        32787 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        48254 => ['type' => self::TYPE_CDN_PROXY, 'provider' => '20i / StackCDN'],
        19551 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'Imperva'],
        60068 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'CDN77 / Datacamp'],
        212238 => ['type' => self::TYPE_CDN_PROXY, 'provider' => 'CDN77 / Datacamp'],
        16509 => ['type' => self::TYPE_CLOUD, 'provider' => 'Amazon AWS'],
        14618 => ['type' => self::TYPE_CLOUD, 'provider' => 'Amazon AWS'],
        8987 => ['type' => self::TYPE_CLOUD, 'provider' => 'Amazon AWS'],
        7224 => ['type' => self::TYPE_CLOUD, 'provider' => 'Amazon AWS'],
        15169 => ['type' => self::TYPE_CLOUD, 'provider' => 'Google Cloud'],
        396982 => ['type' => self::TYPE_CLOUD, 'provider' => 'Google Cloud'],
        36040 => ['type' => self::TYPE_CLOUD, 'provider' => 'Google Cloud'],
        8075 => ['type' => self::TYPE_CLOUD, 'provider' => 'Microsoft Azure'],
        8068 => ['type' => self::TYPE_CLOUD, 'provider' => 'Microsoft Azure'],
        8069 => ['type' => self::TYPE_CLOUD, 'provider' => 'Microsoft Azure'],
        31898 => ['type' => self::TYPE_CLOUD, 'provider' => 'Oracle Cloud'],
        45102 => ['type' => self::TYPE_CLOUD, 'provider' => 'Alibaba Cloud'],
        37963 => ['type' => self::TYPE_CLOUD, 'provider' => 'Alibaba Cloud'],
        36351 => ['type' => self::TYPE_CLOUD, 'provider' => 'IBM Cloud'],
        32934 => ['type' => self::TYPE_CLOUD, 'provider' => 'Meta Platforms'],
        14061 => ['type' => self::TYPE_DATACENTER, 'provider' => 'DigitalOcean'],
        393406 => ['type' => self::TYPE_DATACENTER, 'provider' => 'DigitalOcean'],
        24940 => ['type' => self::TYPE_DATACENTER, 'provider' => 'Hetzner'],
        213230 => ['type' => self::TYPE_DATACENTER, 'provider' => 'Hetzner'],
        16276 => ['type' => self::TYPE_DATACENTER, 'provider' => 'OVHcloud'],
        63949 => ['type' => self::TYPE_DATACENTER, 'provider' => 'Linode / Akamai'],
        20473 => ['type' => self::TYPE_DATACENTER, 'provider' => 'Vultr'],
        51167 => ['type' => self::TYPE_DATACENTER, 'provider' => 'Contabo'],
        12876 => ['type' => self::TYPE_DATACENTER, 'provider' => 'Scaleway'],
        47583 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'Hostinger'],
        22612 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'Namecheap'],
        26496 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'GoDaddy'],
        398101 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'GoDaddy'],
        46606 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'Bluehost / Unified Layer'],
        19871 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'SiteGround'],
        395954 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'SiteGround'],
        26347 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'DreamHost'],
        393560 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'WP Engine'],
        8560 => ['type' => self::TYPE_HOSTING_ISP, 'provider' => 'IONOS / 1&1'],
    ];

    /**
     * Substring signatures checked against CNAME & rDNS/PTR hostnames (highest precision for edge/CDNs).
     *
     * @var array<int, array{pattern: string, type: string, provider: string}>
     */
    private const HOST_SIGNATURES = [
        ['pattern' => 'cloudflare', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Cloudflare'],
        ['pattern' => 'cloudfront.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Amazon CloudFront'],
        ['pattern' => 'akamaiedge.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        ['pattern' => 'edgekey.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        ['pattern' => 'edgesuite.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        ['pattern' => 'akamaitechnologies', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Akamai'],
        ['pattern' => 'fastly', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Fastly'],
        ['pattern' => 'stackcp.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => '20i / StackCDN'],
        ['pattern' => 'stackdns.com', 'type' => self::TYPE_CDN_PROXY, 'provider' => '20i / StackCDN'],
        ['pattern' => 'sucuri.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Sucuri'],
        ['pattern' => 'incapdns.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Imperva'],
        ['pattern' => 'vercel', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Vercel Edge'],
        ['pattern' => 'netlify', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Netlify Edge'],
        ['pattern' => 'b-cdn.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'BunnyCDN'],
        ['pattern' => 'azureedge.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Azure CDN'],
        ['pattern' => 'azurefd.net', 'type' => self::TYPE_CDN_PROXY, 'provider' => 'Azure Front Door'],
        ['pattern' => 'googleusercontent.com', 'type' => self::TYPE_CLOUD, 'provider' => 'Google Cloud'],
        ['pattern' => '1e100.net', 'type' => self::TYPE_CLOUD, 'provider' => 'Google Cloud'],
        ['pattern' => 'amazonaws.com', 'type' => self::TYPE_CLOUD, 'provider' => 'Amazon AWS'],
        ['pattern' => 'digitalocean', 'type' => self::TYPE_DATACENTER, 'provider' => 'DigitalOcean'],
        ['pattern' => 'your-server.de', 'type' => self::TYPE_DATACENTER, 'provider' => 'Hetzner'],
        ['pattern' => 'hetzner', 'type' => self::TYPE_DATACENTER, 'provider' => 'Hetzner'],
        ['pattern' => 'ovh.net', 'type' => self::TYPE_DATACENTER, 'provider' => 'OVHcloud'],
        ['pattern' => 'ovh.ca', 'type' => self::TYPE_DATACENTER, 'provider' => 'OVHcloud'],
        ['pattern' => 'linode', 'type' => self::TYPE_DATACENTER, 'provider' => 'Linode / Akamai'],
        ['pattern' => 'vultr', 'type' => self::TYPE_DATACENTER, 'provider' => 'Vultr'],
    ];

    /**
     * Keyword rules for classifying ASN Organization names when not matched by exact ASN/CIDR.
     *
     * @var array<int, array{keywords: array<int, string>, type: string}>
     */
    private const ORG_KEYWORDS = [
        [
            'type' => self::TYPE_CDN_PROXY,
            'keywords' => ['cloudflare', 'cloudfront', 'fastly', 'akamai', 'sucuri', 'imperva', 'incapsula', 'stackpath', 'bunnycdn', 'keycdn', 'cdn77', 'edgecast', 'limelight', 'stackcdn'],
        ],
        [
            'type' => self::TYPE_CLOUD,
            'keywords' => ['amazon', 'aws', 'google', 'microsoft', 'azure', 'oracle cloud', 'alibaba', 'alibabacloud', 'tencent cloud', 'ibm cloud'],
        ],
        [
            'type' => self::TYPE_DATACENTER,
            'keywords' => ['digitalocean', 'hetzner', 'ovh', 'linode', 'vultr', 'choopa', 'contabo', 'scaleway', 'upcloud', 'leaseweb', 'quadranet', 'psychz', 'datacamp', 'm247', 'colocation'],
        ],
    ];

    /** @var array<string, array> */
    private array $memoryDnsCache = [];

    /** @var array<string, array> */
    private array $memoryIpMetaCache = [];

    /** @var array<int, string> */
    private array $memoryAsnOrgCache = [];

    /** @var callable|null */
    private $dnsResolver = null;

    /** @var callable|null */
    private $ipMetadataResolver = null;

    /**
     * Override the batch DNS resolver (primarily for fast, deterministic unit/feature tests).
     * Signature: fn(array $uniqueHosts): array<string, array{ipv4: array<int, string>, ipv6: array<int, string>, cname: ?string, error?: ?string}>
     */
    public function setDnsResolver(?callable $resolver): self
    {
        $this->dnsResolver = $resolver;
        return $this;
    }

    /**
     * Override the batch IP metadata resolver (primarily for deterministic unit/feature tests).
     * Signature: fn(array $uniqueIps): array<string, array{asn_number?: ?int, asn?: ?string, bgp_prefix?: ?string, country?: ?string, org?: ?string, rdns?: ?string}>
     */
    public function setIpMetadataResolver(?callable $resolver): self
    {
        $this->ipMetadataResolver = $resolver;
        return $this;
    }

    /**
     * Clear in-memory caches on this service instance.
     */
    public function clearMemoryCache(): self
    {
        $this->memoryDnsCache = [];
        $this->memoryIpMetaCache = [];
        $this->memoryAsnOrgCache = [];
        return $this;
    }

    /**
     * Resolve a batch of URLs, domains, or raw IP addresses, returning normalized rows and cluster summaries.
     *
     * @param  array<int, string|null>  $inputs
     * @return array<string, mixed>
     */
    public function resolveBatch(array $inputs, bool $deduplicate = false, bool $enrichMetadata = true): array
    {
        $startedAt = microtime(true);

        $normalizedItems = [];
        $seenHosts = [];
        $uniqueHostsToResolve = [];
        $uniqueRawIps = [];

        foreach ($inputs as $rawInput) {
            $norm = $this->normalizeInput((string) ($rawInput ?? ''));

            if ($deduplicate && $norm['valid'] && $norm['host'] !== null) {
                if (isset($seenHosts[$norm['host']])) {
                    continue;
                }
                $seenHosts[$norm['host']] = true;
            }

            $normalizedItems[] = $norm;

            if ($norm['valid'] && $norm['host'] !== null) {
                if ($norm['is_ip']) {
                    $uniqueRawIps[$norm['host']] = true;
                } else {
                    $uniqueHostsToResolve[$norm['host']] = true;
                }
            }
        }

        // 1. Resolve unique domains (with memory + Laravel cache)
        $dnsResults = $this->resolveHosts(array_keys($uniqueHostsToResolve));

        // 2. Collect unique primary/secondary IPs that need enrichment
        $ipsToEnrich = $uniqueRawIps;
        foreach ($dnsResults as $dns) {
            if (!empty($dns['ipv4'][0])) {
                $ipsToEnrich[$dns['ipv4'][0]] = true;
            } elseif (!empty($dns['ipv6'][0])) {
                $ipsToEnrich[$dns['ipv6'][0]] = true;
            }
        }

        // 3. Enrich unique public IPs (with memory + Laravel cache)
        $ipMetadata = $enrichMetadata
            ? $this->enrichIps(array_keys($ipsToEnrich))
            : [];

        // 4. Assemble per-row results + count shared IP / Class-C clusters in this batch
        $results = [];
        $ipGroups = [];
        $classCGroups = [];
        $resolvedCount = 0;
        $unresolvedCount = 0;
        $invalidCount = 0;

        foreach ($normalizedItems as $norm) {
            $row = $this->buildRowResult($norm, $dnsResults, $ipMetadata);
            $results[] = $row;

            if ($row['status'] === 'invalid') {
                $invalidCount++;
                continue;
            }
            if ($row['status'] === 'unresolved') {
                $unresolvedCount++;
                continue;
            }

            $resolvedCount++;
            $host = $row['host'];

            if ($row['primary_ip'] !== null) {
                $ipGroups[$row['primary_ip']]['hosts'][$host] = true;
                $ipGroups[$row['primary_ip']]['sample'] = $row;
            }
            if ($row['class_c'] !== null) {
                $classCGroups[$row['class_c']]['hosts'][$host] = true;
                if ($row['primary_ip'] !== null) {
                    $classCGroups[$row['class_c']]['ips'][$row['primary_ip']] = true;
                }
            }
        }

        // 5. Annotate batch-level shared IP & same Class-C flags onto every row
        foreach ($results as &$row) {
            $ip = $row['primary_ip'];
            $classC = $row['class_c'];

            $sameIpCount = ($ip !== null && isset($ipGroups[$ip])) ? count($ipGroups[$ip]['hosts']) : 0;
            $sameClassCCount = ($classC !== null && isset($classCGroups[$classC])) ? count($classCGroups[$classC]['hosts']) : 0;

            $row['same_ip_count'] = $sameIpCount;
            $row['is_shared_ip'] = $sameIpCount > 1;
            $row['same_class_c_count'] = $sameClassCCount;
            $row['is_same_class_c'] = $sameClassCCount > 1;
        }
        unset($row);

        $clusters = $this->formatClusterReport($ipGroups, $classCGroups);

        return [
            'success' => true,
            'meta' => [
                'submitted_count' => count($inputs),
                'processed_count' => count($results),
                'unique_hosts_count' => count($uniqueHostsToResolve) + count($uniqueRawIps),
                'resolved_count' => $resolvedCount,
                'unresolved_count' => $unresolvedCount,
                'invalid_count' => $invalidCount,
                'shared_ip_groups_count' => count($clusters['by_ip']),
                'shared_class_c_groups_count' => count($clusters['by_class_c']),
                'elapsed_ms' => round((microtime(true) - $startedAt) * 1000, 1),
            ],
            'results' => $results,
            'clusters' => $clusters,
        ];
    }

    /**
     * Normalize a raw URL, domain, or IP literal into a canonical host.
     *
     * @return array{input: string, host: ?string, is_ip: bool, valid: bool, error: ?string}
     */
    public function normalizeInput(string $raw): array
    {
        // Strip UTF-8 BOM, zero-width spaces, and surrounding whitespace/punctuation
        $cleaned = preg_replace('/[\x{FEFF}\x{200B}-\x{200D}\x{2060}]/u', '', $raw) ?? $raw;
        $rawTrimmed = trim($cleaned);
        $candidate = trim($rawTrimmed, " \t\n\r\0\x0B\"'<>(),;");

        if ($candidate === '') {
            return $this->invalidNormalization($rawTrimmed, 'Empty input');
        }

        // Reject non-web URI schemes immediately
        if (preg_match('/^(javascript|data|vbscript|mailto|tel|file|about):/i', $candidate)) {
            return $this->invalidNormalization($rawTrimmed, 'Unsupported URI scheme');
        }

        // Direct IPv4 or IPv6 literal (including bracketed [ipv6] or [ipv6]:port)
        if ($ipLiteral = $this->extractIpLiteral($candidate)) {
            return [
                'input' => $rawTrimmed,
                'host' => $ipLiteral,
                'is_ip' => true,
                'valid' => true,
                'error' => null,
            ];
        }

        // Reject whitespace or control characters inside the URL/host string
        if (preg_match('/[\s\x00-\x1F\x7F]/', $candidate)) {
            return $this->invalidNormalization($rawTrimmed, 'Invalid whitespace or control characters in URL/domain');
        }

        $forParse = $candidate;
        if (str_starts_with($forParse, '//')) {
            $forParse = 'http:' . $forParse;
        } elseif (!preg_match('/^[a-zA-Z][a-zA-Z0-9+\-.]*:\/\//', $forParse)) {
            // Reject malformed scheme:value unless it is host:port or user:pass@host
            if (preg_match('/^[a-zA-Z][a-zA-Z0-9+\-.]*:(?!\d+(?:[\/?#]|$))/', $forParse) && !preg_match('/^[^\/?#]+@[^\/?#]+/', $forParse)) {
                return $this->invalidNormalization($rawTrimmed, 'Invalid URL format');
            }
            $forParse = 'http://' . $forParse;
        }

        $parts = @parse_url($forParse);
        if (!is_array($parts) || empty($parts['host'])) {
            return $this->invalidNormalization($rawTrimmed, 'Could not parse hostname');
        }

        $host = trim($parts['host'], "[]");
        $host = rtrim(mb_strtolower($host, 'UTF-8'), '.');

        if ($host === '') {
            return $this->invalidNormalization($rawTrimmed, 'Empty hostname');
        }

        // Check if parsed host was an IPv4/IPv6 address (e.g. http://192.168.1.1:8080/admin)
        if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            $canonical = $this->canonicalizeIp($host);
            return [
                'input' => $rawTrimmed,
                'host' => $canonical,
                'is_ip' => true,
                'valid' => true,
                'error' => null,
            ];
        }

        // Convert IDN Unicode domains to Punycode ASCII
        if (preg_match('/[^\x20-\x7E]/', $host)) {
            if (!function_exists('idn_to_ascii')) {
                return $this->invalidNormalization($rawTrimmed, 'Internationalized domain conversion unavailable');
            }
            $variant = defined('INTL_IDNA_VARIANT_UTS46') ? INTL_IDNA_VARIANT_UTS46 : 0;
            $ascii = @idn_to_ascii($host, IDNA_DEFAULT, $variant);
            if ($ascii === false || $ascii === '') {
                return $this->invalidNormalization($rawTrimmed, 'Invalid IDN domain');
            }
            $host = strtolower($ascii);
        }

        if (!$this->isValidFqdn($host)) {
            return $this->invalidNormalization($rawTrimmed, 'Invalid domain syntax');
        }

        return [
            'input' => $rawTrimmed,
            'host' => $host,
            'is_ip' => false,
            'valid' => true,
            'error' => null,
        ];
    }

    /**
     * Classify an IPv4 or IPv6 address's version, classful block, scope, and subnet CIDRs.
     *
     * @return array{
     *     ip_version: int,
     *     ip_version_label: string,
     *     ip_class: string,
     *     ip_scope: string,
     *     is_private_or_reserved: bool,
     *     special_provider: ?string,
     *     class_c: ?string,
     *     class_c_prefix: ?string,
     *     class_b: ?string,
     *     ipv6_subnet_64: ?string,
     *     ipv6_subnet_48: ?string
     * }
     */
    public function classifyIpBlock(string $ip): array
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $octets = explode('.', $ip);
            $first = (int) $octets[0];

            $ipClass = match (true) {
                $first <= 127 => 'Class A',
                $first <= 191 => 'Class B',
                $first <= 223 => 'Class C',
                $first <= 239 => 'Class D (Multicast)',
                default => 'Class E (Reserved)',
            };

            $scope = 'Public';
            $isSpecial = false;
            $specialProvider = null;

            foreach (self::IPV4_SPECIAL_RANGES as $range) {
                if (self::ipInCidr($ip, $range['cidr'])) {
                    $scope = $range['scope'];
                    $isSpecial = true;
                    $specialProvider = $range['provider'];
                    break;
                }
            }

            return [
                'ip_version' => 4,
                'ip_version_label' => 'IPv4',
                'ip_class' => $ipClass,
                'ip_scope' => $scope,
                'is_private_or_reserved' => $isSpecial,
                'special_provider' => $specialProvider,
                'class_c' => "{$octets[0]}.{$octets[1]}.{$octets[2]}.0/24",
                'class_c_prefix' => "{$octets[0]}.{$octets[1]}.{$octets[2]}",
                'class_b' => "{$octets[0]}.{$octets[1]}.0.0/16",
                'ipv6_subnet_64' => null,
                'ipv6_subnet_48' => null,
            ];
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $scope = 'IPv6 Global Unicast';
            $isSpecial = false;
            $specialProvider = null;

            foreach (self::IPV6_SPECIAL_RANGES as $range) {
                if (self::ipInCidr($ip, $range['cidr'])) {
                    $scope = $range['scope'];
                    $isSpecial = true;
                    $specialProvider = $range['provider'];
                    break;
                }
            }

            return [
                'ip_version' => 6,
                'ip_version_label' => 'IPv6',
                'ip_class' => 'IPv6',
                'ip_scope' => $scope,
                'is_private_or_reserved' => $isSpecial,
                'special_provider' => $specialProvider,
                'class_c' => null,
                'class_c_prefix' => null,
                'class_b' => null,
                'ipv6_subnet_64' => self::maskIpToCidr($ip, 64),
                'ipv6_subnet_48' => self::maskIpToCidr($ip, 48),
            ];
        }

        return [
            'ip_version' => 0,
            'ip_version_label' => 'Unknown',
            'ip_class' => 'Unknown',
            'ip_scope' => 'Unknown',
            'is_private_or_reserved' => true,
            'special_provider' => null,
            'class_c' => null,
            'class_c_prefix' => null,
            'class_b' => null,
            'ipv6_subnet_64' => null,
            'ipv6_subnet_48' => null,
        ];
    }

    /**
     * Classify infrastructure/hosting IP type using block scope, CNAME/PTR signatures, local CIDRs, ASN, and Org name.
     *
     * @param  array<string, mixed>  $blockInfo
     * @param  array<string, mixed>  $meta
     * @return array{ip_type: string, ip_type_label: string, provider: ?string}
     */
    public function classifyInfrastructure(
        ?string $ip,
        array $blockInfo,
        array $meta = [],
        ?string $cname = null
    ): array {
        if ($ip === null || $ip === '') {
            return $this->makeInfraResult(self::TYPE_UNRESOLVED, null);
        }

        if (!empty($blockInfo['is_private_or_reserved'])) {
            return $this->makeInfraResult(
                self::TYPE_PRIVATE_BOGON,
                $blockInfo['special_provider'] ?? $blockInfo['ip_scope']
            );
        }

        // 1. High-precision CNAME & rDNS/PTR edge signatures (e.g. CloudFront, Akamai, StackCDN)
        $signals = array_filter([
            strtolower((string) $cname),
            strtolower((string) ($meta['rdns'] ?? '')),
        ]);
        foreach ($signals as $signal) {
            foreach (self::HOST_SIGNATURES as $sig) {
                if (str_contains($signal, $sig['pattern'])) {
                    return $this->makeInfraResult($sig['type'], $sig['provider']);
                }
            }
        }

        // 2. Fast local CIDR table
        foreach (self::KNOWN_CIDR_PROVIDERS as $entry) {
            foreach ($entry['cidrs'] as $cidr) {
                if (self::ipInCidr($ip, $cidr)) {
                    return $this->makeInfraResult($entry['type'], $entry['provider']);
                }
            }
        }

        // 3. Known ASN number mapping
        $asnNumber = $meta['asn_number'] ?? null;
        if ($asnNumber !== null && isset(self::KNOWN_ASNS[(int) $asnNumber])) {
            $match = self::KNOWN_ASNS[(int) $asnNumber];
            return $this->makeInfraResult($match['type'], $match['provider']);
        }

        // 4. Org keyword matching from Cymru ASN data
        $org = (string) ($meta['org'] ?? '');
        $orgLower = strtolower($org);
        if ($orgLower !== '') {
            foreach (self::ORG_KEYWORDS as $rule) {
                foreach ($rule['keywords'] as $kw) {
                    if (str_contains($orgLower, $kw)) {
                        return $this->makeInfraResult($rule['type'], $this->cleanOrgDisplayName($org));
                    }
                }
            }
        }

        return $this->makeInfraResult(
            self::TYPE_HOSTING_ISP,
            $org !== '' ? $this->cleanOrgDisplayName($org) : 'Public Network'
        );
    }

    /**
     * Test whether an IPv4 or IPv6 address belongs to a CIDR block using binary prefix masking.
     */
    public static function ipInCidr(string $ip, string $cidr): bool
    {
        if (!str_contains($cidr, '/')) {
            $a = @inet_pton($ip);
            $b = @inet_pton($cidr);
            return $a !== false && $b !== false && $a === $b;
        }

        [$subnet, $prefixStr] = explode('/', $cidr, 2);
        $ipBin = @inet_pton($ip);
        $subnetBin = @inet_pton($subnet);

        if ($ipBin === false || $subnetBin === false || strlen($ipBin) !== strlen($subnetBin)) {
            return false;
        }

        $prefix = (int) $prefixStr;
        $maxBits = strlen($ipBin) * 8;
        if ($prefix < 0 || $prefix > $maxBits) {
            return false;
        }

        $bytes = intdiv($prefix, 8);
        $remainderBits = $prefix % 8;

        if ($bytes > 0 && substr($ipBin, 0, $bytes) !== substr($subnetBin, 0, $bytes)) {
            return false;
        }

        if ($remainderBits > 0) {
            $mask = (0xFF << (8 - $remainderBits)) & 0xFF;
            if ((ord($ipBin[$bytes]) & $mask) !== (ord($subnetBin[$bytes]) & $mask)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Apply a CIDR netmask prefix to an IPv4 or IPv6 address and return canonical CIDR notation.
     */
    public static function maskIpToCidr(string $ip, int $prefix): ?string
    {
        $bin = @inet_pton($ip);
        if ($bin === false) {
            return null;
        }

        $len = strlen($bin);
        $maxBits = $len * 8;
        if ($prefix < 0 || $prefix > $maxBits) {
            return null;
        }

        $bytes = intdiv($prefix, 8);
        $remainder = $prefix % 8;

        $masked = '';
        for ($i = 0; $i < $len; $i++) {
            if ($i < $bytes) {
                $masked .= $bin[$i];
            } elseif ($i === $bytes && $remainder > 0) {
                $mask = (0xFF << (8 - $remainder)) & 0xFF;
                $masked .= chr(ord($bin[$i]) & $mask);
            } else {
                $masked .= "\0";
            }
        }

        $network = @inet_ntop($masked);
        return $network !== false ? "{$network}/{$prefix}" : null;
    }

    /**
     * Resolve DNS (A, AAAA, CNAME) for unique hostnames using memory cache, Laravel file cache, and concurrent DoH.
     *
     * @param  array<int, string>  $uniqueHosts
     * @return array<string, array{ipv4: array<int, string>, ipv6: array<int, string>, cname: ?string, cached: bool}>
     */
    protected function resolveHosts(array $uniqueHosts): array
    {
        if (empty($uniqueHosts)) {
            return [];
        }

        $resolved = [];
        $uncached = [];

        foreach ($uniqueHosts as $host) {
            if (isset($this->memoryDnsCache[$host])) {
                $resolved[$host] = $this->memoryDnsCache[$host] + ['cached' => true];
                continue;
            }

            $cached = $this->cacheGet("bulk_ip:dns:{$host}");
            if (is_array($cached)) {
                $this->memoryDnsCache[$host] = $cached;
                $resolved[$host] = $cached + ['cached' => true];
                continue;
            }

            $uncached[] = $host;
        }

        if (!empty($uncached)) {
            $fresh = $this->dnsResolver !== null
                ? ($this->dnsResolver)($uncached)
                : $this->resolveHostsViaDohAndFallback($uncached);

            foreach ($uncached as $host) {
                $entry = [
                    'ipv4' => array_values(array_unique(array_filter(
                        $fresh[$host]['ipv4'] ?? [],
                        fn ($ip) => (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
                    ))),
                    'ipv6' => array_values(array_unique(array_filter(
                        array_map(fn ($ip) => $this->canonicalizeIp((string) $ip), $fresh[$host]['ipv6'] ?? []),
                        fn ($ip) => (bool) filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)
                    ))),
                    'cname' => $fresh[$host]['cname'] ?? null,
                ];

                $this->memoryDnsCache[$host] = $entry;
                // Cache positive DNS resolutions (and brief negative cache for NXDOMAIN only in memory)
                if (!empty($entry['ipv4']) || !empty($entry['ipv6'])) {
                    $this->cachePut("bulk_ip:dns:{$host}", $entry, self::DNS_CACHE_TTL_SECONDS);
                }

                $resolved[$host] = $entry + ['cached' => false];
            }
        }

        return $resolved;
    }

    /**
     * Resolve A and AAAA records concurrently via curl_multi DoH, falling back to Google DoH and native DNS.
     *
     * @param  array<int, string>  $hosts
     * @return array<string, array{ipv4: array<int, string>, ipv6: array<int, string>, cname: ?string}>
     */
    protected function resolveHostsViaDohAndFallback(array $hosts): array
    {
        $out = [];
        foreach ($hosts as $host) {
            $out[$host] = ['ipv4' => [], 'ipv6' => [], 'cname' => null];
        }

        if (!function_exists('curl_multi_init')) {
            foreach ($hosts as $host) {
                $out[$host] = $this->resolveHostNativeFallback($host);
            }
            return $out;
        }

        $queries = [];
        foreach ($hosts as $host) {
            $queries[] = ['key' => "{$host}|A", 'host' => $host, 'name' => $host, 'type' => 'A'];
            $queries[] = ['key' => "{$host}|AAAA", 'host' => $host, 'name' => $host, 'type' => 'AAAA'];
        }

        $primary = $this->executeDohWithFailover($queries, 'cloudflare', 'google');
        $failedHosts = [];

        foreach ($queries as $q) {
            $res = $primary[$q['key']] ?? null;
            if ($res !== null && $res['ok']) {
                $this->applyDohDnsAnswer($out[$q['host']], $q['type'], $res['answers']);
            } else {
                $failedHosts[$q['host']] = true;
            }
        }

        foreach (array_keys($failedHosts) as $host) {
            $native = $this->resolveHostNativeFallback($host);
            if (empty($out[$host]['ipv4'])) {
                $out[$host]['ipv4'] = $native['ipv4'];
            }
            if (empty($out[$host]['ipv6'])) {
                $out[$host]['ipv6'] = $native['ipv6'];
            }
            if ($out[$host]['cname'] === null) {
                $out[$host]['cname'] = $native['cname'];
            }
        }

        return $out;
    }

    /**
     * Native PHP DNS fallback when DoH endpoints are unreachable.
     *
     * @return array{ipv4: array<int, string>, ipv6: array<int, string>, cname: ?string}
     */
    protected function resolveHostNativeFallback(string $host): array
    {
        $ipv4 = [];
        $ipv6 = [];
        $cname = null;

        if (function_exists('dns_get_record')) {
            $aRecords = @dns_get_record($host, DNS_A);
            if (is_array($aRecords)) {
                foreach ($aRecords as $rec) {
                    if (!empty($rec['ip']) && filter_var($rec['ip'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $ipv4[] = $rec['ip'];
                    }
                }
            }

            $aaaaRecords = @dns_get_record($host, DNS_AAAA);
            if (is_array($aaaaRecords)) {
                foreach ($aaaaRecords as $rec) {
                    if (!empty($rec['ipv6']) && filter_var($rec['ipv6'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                        $ipv6[] = $this->canonicalizeIp($rec['ipv6']);
                    }
                }
            }

            $cnameRecords = @dns_get_record($host, DNS_CNAME);
            if (is_array($cnameRecords) && !empty($cnameRecords[0]['target'])) {
                $cname = rtrim(strtolower((string) $cnameRecords[0]['target']), '.');
            }
        }

        if (empty($ipv4) && function_exists('gethostbynamel')) {
            $list = @gethostbynamel($host);
            if (is_array($list)) {
                foreach ($list as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                        $ipv4[] = $ip;
                    }
                }
            }
        }

        return [
            'ipv4' => array_values(array_unique($ipv4)),
            'ipv6' => array_values(array_unique($ipv6)),
            'cname' => $cname,
        ];
    }

    /**
     * Enrich unique public IPs with ASN, BGP prefix, country, org/ISP, and rDNS/PTR metadata.
     *
     * @param  array<int, string>  $uniqueIps
     * @return array<string, array<string, mixed>>
     */
    protected function enrichIps(array $uniqueIps): array
    {
        if (empty($uniqueIps)) {
            return [];
        }

        $enriched = [];
        $uncachedPublic = [];

        foreach ($uniqueIps as $ip) {
            $block = $this->classifyIpBlock($ip);
            if ($block['is_private_or_reserved']) {
                $enriched[$ip] = $this->emptyIpMetadata();
                continue;
            }

            if (isset($this->memoryIpMetaCache[$ip])) {
                $enriched[$ip] = $this->memoryIpMetaCache[$ip];
                continue;
            }

            $cached = $this->cacheGet("bulk_ip:meta:{$ip}");
            if (is_array($cached) && !empty($cached['asn_number'])) {
                $this->memoryIpMetaCache[$ip] = $cached;
                $enriched[$ip] = $cached;
                continue;
            }

            $uncachedPublic[] = $ip;
        }

        if (!empty($uncachedPublic)) {
            $fresh = $this->ipMetadataResolver !== null
                ? ($this->ipMetadataResolver)($uncachedPublic)
                : $this->enrichIpsViaCymruAndPtr($uncachedPublic);

            foreach ($uncachedPublic as $ip) {
                $meta = $this->normalizeMetadataPayload($fresh[$ip] ?? []);
                $this->memoryIpMetaCache[$ip] = $meta;

                // Never poison the 24h persistent cache when ASN lookup failed transiently
                if ($meta['asn_number'] !== null) {
                    $this->cachePut("bulk_ip:meta:{$ip}", $meta, self::IP_META_CACHE_TTL_SECONDS);
                }
                $enriched[$ip] = $meta;
            }
        }

        return $enriched;
    }

    /**
     * Perform concurrent Team Cymru ASN TXT + rDNS PTR lookups via DoH with native DNS TXT fallback.
     *
     * @param  array<int, string>  $ips
     * @return array<string, array<string, mixed>>
     */
    protected function enrichIpsViaCymruAndPtr(array $ips): array
    {
        $out = [];
        foreach ($ips as $ip) {
            $out[$ip] = $this->emptyIpMetadata();
        }

        // Batch 1: Cymru origin TXT + PTR for all uncached IPs concurrently via DoH
        $queries = [];
        $cymruQueries = [];
        foreach ($ips as $ip) {
            $cymruName = $this->formatReverseIpHost($ip, 'origin.asn.cymru.com', 'origin6.asn.cymru.com');
            if ($cymruName !== null) {
                $q = ['key' => "{$ip}|CYMRU", 'ip' => $ip, 'kind' => 'CYMRU', 'name' => $cymruName, 'type' => 'TXT'];
                $queries[] = $q;
                $cymruQueries[] = $q;
            }
            $ptrName = $this->formatReverseIpHost($ip, 'in-addr.arpa', 'ip6.arpa');
            if ($ptrName !== null) {
                $queries[] = ['key' => "{$ip}|PTR", 'ip' => $ip, 'kind' => 'PTR', 'name' => $ptrName, 'type' => 'PTR'];
            }
        }

        $responses = $this->executeDohWithFailover($queries, 'google', 'cloudflare');

        foreach ($queries as $q) {
            if ($q['kind'] !== 'PTR') {
                continue;
            }
            $res = $responses[$q['key']] ?? null;
            if ($res === null || !$res['ok'] || empty($res['answers'])) {
                continue;
            }
            foreach ($res['answers'] as $ans) {
                if (($ans['type'] ?? 0) === 12 && !empty($ans['data'])) {
                    $out[$q['ip']]['rdns'] = rtrim(strtolower((string) $ans['data']), '.');
                    break;
                }
            }
        }

        $parsedOrigins = $this->parseTxtResultsWithNativeFallback(
            $cymruQueries,
            $responses,
            fn (string $txt): ?array => $this->parseCymruOriginTxt($txt),
            15.0
        );

        $asnNumbersNeeded = [];
        foreach ($cymruQueries as $q) {
            $parsed = $parsedOrigins[$q['key']] ?? null;
            if ($parsed === null) {
                continue;
            }
            $ip = $q['ip'];
            $out[$ip]['asn_number'] = $parsed['asn_number'];
            $out[$ip]['asn'] = 'AS' . $parsed['asn_number'];
            $out[$ip]['bgp_prefix'] = $parsed['bgp_prefix'];
            $out[$ip]['country'] = $parsed['country'];
            $asnNumbersNeeded[$parsed['asn_number']] = true;
        }

        // Batch 2: Resolve AS<num>.asn.cymru.com organization names (cached per ASN)
        $asnNames = $this->resolveAsnOrgNames(array_keys($asnNumbersNeeded));
        foreach ($out as $ip => &$meta) {
            if ($meta['asn_number'] !== null && isset($asnNames[$meta['asn_number']])) {
                $meta['org'] = $asnNames[$meta['asn_number']];
            }
        }
        unset($meta);

        return $out;
    }

    /**
     * Resolve and cache organization names for ASN numbers via Team Cymru AS TXT records.
     *
     * @param  array<int, int>  $asnNumbers
     * @return array<int, string>
     */
    protected function resolveAsnOrgNames(array $asnNumbers): array
    {
        $names = [];
        $uncached = [];

        foreach ($asnNumbers as $asn) {
            $asn = (int) $asn;
            if ($asn <= 0) {
                continue;
            }
            if (isset($this->memoryAsnOrgCache[$asn])) {
                $names[$asn] = $this->memoryAsnOrgCache[$asn];
                continue;
            }
            $cached = $this->cacheGet("bulk_ip:asn:{$asn}");
            if (is_string($cached) && $cached !== '') {
                $this->memoryAsnOrgCache[$asn] = $cached;
                $names[$asn] = $cached;
                continue;
            }
            $uncached[] = $asn;
        }

        if (empty($uncached)) {
            return $names;
        }

        $queries = array_map(fn (int $asn) => [
            'key' => "AS{$asn}",
            'asn' => $asn,
            'name' => "AS{$asn}.asn.cymru.com",
            'type' => 'TXT',
        ], $uncached);

        $responses = $this->executeDohWithFailover($queries, 'google', 'cloudflare');
        $parsedOrgs = $this->parseTxtResultsWithNativeFallback(
            $queries,
            $responses,
            fn (string $txt): ?string => $this->parseCymruAsnTxt($txt),
            10.0
        );

        foreach ($queries as $q) {
            $asn = $q['asn'];
            $org = $parsedOrgs[$q['key']] ?? null;
            if ($org !== null) {
                $this->memoryAsnOrgCache[$asn] = $org;
                $this->cachePut("bulk_ip:asn:{$asn}", $org, self::ASN_CACHE_TTL_SECONDS);
                $names[$asn] = $org;
            }
        }

        return $names;
    }

    /**
     * Parse TXT records from DoH responses and fall back to native dns_get_record(..., DNS_TXT) when DoH is throttled.
     *
     * @param  array<int, array<string, mixed>>  $queries
     * @param  array<string, array<string, mixed>>  $dohResponses
     * @return array<string, mixed>
     */
    protected function parseTxtResultsWithNativeFallback(
        array $queries,
        array $dohResponses,
        callable $parser,
        float $nativeBudgetSeconds = 12.0
    ): array {
        $parsedByKey = [];
        $missing = [];

        foreach ($queries as $q) {
            $key = $q['key'];
            $res = $dohResponses[$key] ?? null;
            $parsed = null;

            if ($res !== null && !empty($res['ok']) && !empty($res['answers'])) {
                foreach ($res['answers'] as $ans) {
                    if (($ans['type'] ?? 0) !== 16 || empty($ans['data'])) {
                        continue;
                    }
                    $parsed = $parser((string) $ans['data']);
                    if ($parsed !== null) {
                        break;
                    }
                }
            }

            if ($parsed !== null) {
                $parsedByKey[$key] = $parsed;
            } else {
                $missing[] = $q;
            }
        }

        $deadline = microtime(true) + $nativeBudgetSeconds;
        foreach ($missing as $q) {
            if (microtime(true) >= $deadline) {
                break;
            }
            foreach ($this->resolveTxtRecordsNative((string) $q['name']) as $txt) {
                $parsed = $parser($txt);
                if ($parsed !== null) {
                    $parsedByKey[$q['key']] = $parsed;
                    break;
                }
            }
        }

        return $parsedByKey;
    }

    /**
     * Native PHP DNS TXT lookup fallback (used when DoH servers are throttled by Team Cymru).
     *
     * @return array<int, string>
     */
    protected function resolveTxtRecordsNative(string $hostname): array
    {
        if (!function_exists('dns_get_record')) {
            return [];
        }

        $records = @dns_get_record($hostname, DNS_TXT);
        if (!is_array($records)) {
            return [];
        }

        $texts = [];
        foreach ($records as $rec) {
            if (!empty($rec['txt']) && is_string($rec['txt'])) {
                $texts[] = $rec['txt'];
            } elseif (!empty($rec['entries']) && is_array($rec['entries'])) {
                $joined = implode('', array_filter($rec['entries'], 'is_string'));
                if ($joined !== '') {
                    $texts[] = $joined;
                }
            }
        }

        return $texts;
    }

    /**
     * Execute DoH queries against a primary provider and retry failed queries on a fallback provider.
     *
     * @param  array<int, array{key: string, name: string, type: string}>  $queries
     * @return array<string, array{ok: bool, status: int, answers: array<int, array<string, mixed>>}>
     */
    protected function executeDohWithFailover(array $queries, string $primary = 'cloudflare', string $fallback = 'google'): array
    {
        $responses = $this->executeDohBatch($queries, $primary);
        $retryQueries = [];

        foreach ($queries as $q) {
            if (empty($responses[$q['key']]['ok'])) {
                $retryQueries[] = $q;
            }
        }

        if (!empty($retryQueries)) {
            $secondary = $this->executeDohBatch($retryQueries, $fallback);
            foreach ($secondary as $key => $res) {
                if (!empty($res['ok'])) {
                    $responses[$key] = $res;
                }
            }
        }

        return $responses;
    }

    /**
     * Execute a batch of DNS-over-HTTPS JSON queries concurrently via curl_multi with HTTP/2 multiplexing.
     *
     * @param  array<int, array{key: string, name: string, type: string}>  $queries
     * @return array<string, array{ok: bool, status: int, answers: array<int, array<string, mixed>>}>
     */
    protected function executeDohBatch(array $queries, string $provider = 'cloudflare'): array
    {
        if (empty($queries) || !function_exists('curl_multi_init')) {
            return [];
        }

        $mh = curl_multi_init();
        if (defined('CURLMOPT_PIPELINING') && defined('CURLPIPE_MULTIPLEX')) {
            @curl_multi_setopt($mh, CURLMOPT_PIPELINING, CURLPIPE_MULTIPLEX);
        }
        if (defined('CURLMOPT_MAX_HOST_CONNECTIONS')) {
            @curl_multi_setopt($mh, CURLMOPT_MAX_HOST_CONNECTIONS, 6);
        }

        $handles = [];
        foreach ($queries as $q) {
            $url = $provider === 'google'
                ? 'https://dns.google/resolve?name=' . rawurlencode($q['name']) . '&type=' . rawurlencode($q['type'])
                : 'https://cloudflare-dns.com/dns-query?name=' . rawurlencode($q['name']) . '&type=' . rawurlencode($q['type']);

            $ch = curl_init($url);
            $opts = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Accept: application/dns-json'],
                CURLOPT_USERAGENT => 'ShrawanEffects-BulkIpChecker/1.0',
                CURLOPT_CONNECTTIMEOUT => self::DOH_CONNECT_TIMEOUT,
                CURLOPT_TIMEOUT => self::DOH_REQUEST_TIMEOUT,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_SSL_VERIFYPEER => true,
            ];
            if (defined('CURL_HTTP_VERSION_2_0')) {
                $opts[CURLOPT_HTTP_VERSION] = CURL_HTTP_VERSION_2_0;
            }
            curl_setopt_array($ch, $opts);
            curl_multi_add_handle($mh, $ch);
            $handles[$q['key']] = $ch;
        }

        do {
            $execStatus = curl_multi_exec($mh, $active);
            if ($active) {
                curl_multi_select($mh, 0.2);
            }
        } while ($active && $execStatus === CURLM_OK);

        $results = [];
        foreach ($handles as $key => $ch) {
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $body = curl_multi_getcontent($ch);
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);

            if ($httpCode !== 200 || !is_string($body) || $body === '') {
                $results[$key] = ['ok' => false, 'status' => -1, 'answers' => []];
                continue;
            }

            $decoded = json_decode($body, true);
            if (!is_array($decoded) || !array_key_exists('Status', $decoded)) {
                $results[$key] = ['ok' => false, 'status' => -1, 'answers' => []];
                continue;
            }

            $dnsStatus = (int) $decoded['Status'];
            // DNS Status 0 (NOERROR) and 3 (NXDOMAIN) are authoritative non-transient responses
            $isAuthoritative = in_array($dnsStatus, [0, 3], true);
            $results[$key] = [
                'ok' => $isAuthoritative,
                'status' => $dnsStatus,
                'answers' => is_array($decoded['Answer'] ?? null) ? $decoded['Answer'] : [],
            ];
        }

        curl_multi_close($mh);

        return $results;
    }

    /**
     * Build a single normalized, classified, and enriched row result.
     *
     * @param  array<string, mixed>  $norm
     * @param  array<string, array<string, mixed>>  $dnsResults
     * @param  array<string, array<string, mixed>>  $ipMetadata
     * @return array<string, mixed>
     */
    protected function buildRowResult(array $norm, array $dnsResults, array $ipMetadata): array
    {
        if (!$norm['valid'] || $norm['host'] === null) {
            return $this->emptyRowResult(
                $norm['input'],
                null,
                false,
                'invalid',
                $norm['error'] ?? 'Invalid domain or URL'
            );
        }

        $host = $norm['host'];
        if ($norm['is_ip']) {
            $isV4 = (bool) filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            $ipv4All = $isV4 ? [$host] : [];
            $ipv6All = $isV4 ? [] : [$host];
            $cname = null;
            $cached = false;
        } else {
            $dns = $dnsResults[$host] ?? ['ipv4' => [], 'ipv6' => [], 'cname' => null, 'cached' => false];
            $ipv4All = $dns['ipv4'] ?? [];
            $ipv6All = $dns['ipv6'] ?? [];
            $cname = $dns['cname'] ?? null;
            $cached = (bool) ($dns['cached'] ?? false);
        }

        $ipv4 = $ipv4All[0] ?? null;
        $ipv6 = $ipv6All[0] ?? null;
        $primaryIp = $ipv4 ?? $ipv6;

        if ($primaryIp === null) {
            return $this->emptyRowResult(
                $norm['input'],
                $host,
                true,
                'unresolved',
                'No A or AAAA DNS record found',
                $cname,
                $cached
            );
        }

        $blockInfo = $this->classifyIpBlock($primaryIp);
        $meta = $ipMetadata[$primaryIp] ?? $this->emptyIpMetadata();
        $infra = $this->classifyInfrastructure($primaryIp, $blockInfo, $meta, $cname);

        // Include IPv6 /64 subnet even when primary IP is IPv4 if an AAAA record also exists
        $ipv6Subnet64 = $blockInfo['ipv6_subnet_64'];
        if ($ipv6Subnet64 === null && $ipv6 !== null) {
            $ipv6Subnet64 = self::maskIpToCidr($ipv6, 64);
        }

        return [
            'input' => $norm['input'],
            'host' => $host,
            'valid' => true,
            'status' => 'resolved',
            'error' => null,
            'ipv4' => $ipv4,
            'ipv4_all' => $ipv4All,
            'ipv6' => $ipv6,
            'ipv6_all' => $ipv6All,
            'cname' => $cname,
            'primary_ip' => $primaryIp,
            'ip_version' => $blockInfo['ip_version'],
            'ip_version_label' => $blockInfo['ip_version_label'],
            'ip_class' => $blockInfo['ip_class'],
            'ip_scope' => $blockInfo['ip_scope'],
            'class_c' => $blockInfo['class_c'],
            'class_c_prefix' => $blockInfo['class_c_prefix'],
            'class_b' => $blockInfo['class_b'],
            'ipv6_subnet_64' => $ipv6Subnet64,
            'ip_type' => $infra['ip_type'],
            'ip_type_label' => $infra['ip_type_label'],
            'provider' => $infra['provider'],
            'asn' => $meta['asn'],
            'asn_number' => $meta['asn_number'],
            'bgp_prefix' => $meta['bgp_prefix'],
            'country' => $meta['country'],
            'org' => $meta['org'],
            'rdns' => $meta['rdns'],
            'same_ip_count' => 1,
            'is_shared_ip' => false,
            'same_class_c_count' => $blockInfo['class_c'] !== null ? 1 : 0,
            'is_same_class_c' => false,
            'cached' => $cached,
        ];
    }

    /**
     * Format shared IP and shared Class-C subnet clusters for the batch response.
     *
     * @param  array<string, array{hosts: array<string, bool>, sample: array<string, mixed>}>  $ipGroups
     * @param  array<string, array{hosts: array<string, bool>, ips?: array<string, bool>}>  $classCGroups
     * @return array{by_ip: array<int, array<string, mixed>>, by_class_c: array<int, array<string, mixed>>}
     */
    protected function formatClusterReport(array $ipGroups, array $classCGroups): array
    {
        $byIp = [];
        foreach ($ipGroups as $ip => $group) {
            $hosts = array_keys($group['hosts']);
            if (count($hosts) < 2) {
                continue;
            }
            $sample = $group['sample'];
            $byIp[] = [
                'ip' => $ip,
                'count' => count($hosts),
                'ip_type' => $sample['ip_type'],
                'ip_type_label' => $sample['ip_type_label'],
                'provider' => $sample['provider'],
                'asn' => $sample['asn'],
                'org' => $sample['org'],
                'country' => $sample['country'],
                'class_c' => $sample['class_c'],
                'hosts' => $hosts,
            ];
        }
        usort($byIp, fn (array $a, array $b) => $b['count'] <=> $a['count']);

        $byClassC = [];
        foreach ($classCGroups as $classC => $group) {
            $hosts = array_keys($group['hosts']);
            if (count($hosts) < 2) {
                continue;
            }
            $byClassC[] = [
                'class_c' => $classC,
                'count' => count($hosts),
                'ips' => array_keys($group['ips'] ?? []),
                'hosts' => $hosts,
            ];
        }
        usort($byClassC, fn (array $a, array $b) => $b['count'] <=> $a['count']);

        return [
            'by_ip' => $byIp,
            'by_class_c' => $byClassC,
        ];
    }

    /**
     * Extract and canonicalize a raw IPv4 or IPv6 literal (with optional brackets or :port).
     */
    private function extractIpLiteral(string $candidate): ?string
    {
        if (filter_var($candidate, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6)) {
            return $this->canonicalizeIp($candidate);
        }

        if (preg_match('/^\[([^\]]+)\](?::\d+)?(?:[\/?#].*)?$/', $candidate, $m)) {
            if (filter_var($m[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                return $this->canonicalizeIp($m[1]);
            }
        }

        if (preg_match('/^(\d{1,3}(?:\.\d{1,3}){3})(?::\d+)?(?:[\/?#].*)?$/', $candidate, $m)) {
            if (filter_var($m[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                return $m[1];
            }
        }

        return null;
    }

    /**
     * Canonicalize an IPv6 (or IPv4) string via inet_pton/inet_ntop.
     */
    private function canonicalizeIp(string $ip): string
    {
        $bin = @inet_pton($ip);
        if ($bin === false) {
            return $ip;
        }
        $canon = @inet_ntop($bin);
        return $canon !== false ? $canon : $ip;
    }

    /**
     * Validate ASCII Punycode hostname syntax (requires >= 2 labels and non-numeric TLD).
     */
    private function isValidFqdn(string $host): bool
    {
        $len = strlen($host);
        if ($len < 3 || $len > 253 || !str_contains($host, '.')) {
            return false;
        }

        $labels = explode('.', $host);
        if (count($labels) < 2) {
            return false;
        }

        foreach ($labels as $label) {
            if (!preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $label)) {
                return false;
            }
        }

        $tld = end($labels);
        // Reject purely numeric TLDs (catches malformed IPs like 999.999.999.999)
        if (ctype_digit($tld) || strlen($tld) < 2) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $bucket
     * @param  array<int, array<string, mixed>>  $answers
     */
    private function applyDohDnsAnswer(array &$bucket, string $type, array $answers): void
    {
        foreach ($answers as $ans) {
            $ansType = (int) ($ans['type'] ?? 0);
            $data = trim((string) ($ans['data'] ?? ''));
            if ($data === '') {
                continue;
            }

            if ($ansType === 5 && $bucket['cname'] === null) {
                $bucket['cname'] = rtrim(strtolower($data), '.');
            } elseif ($type === 'A' && $ansType === 1 && filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                if (!in_array($data, $bucket['ipv4'], true)) {
                    $bucket['ipv4'][] = $data;
                }
            } elseif ($type === 'AAAA' && $ansType === 28 && filter_var($data, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                $canon = $this->canonicalizeIp($data);
                if (!in_array($canon, $bucket['ipv6'], true)) {
                    $bucket['ipv6'][] = $canon;
                }
            }
        }
    }

    /**
     * Build a reverse-DNS hostname for IPv4 or IPv6 (used for Team Cymru origin TXT and PTR lookups).
     */
    private function formatReverseIpHost(string $ip, string $v4Suffix, string $v6Suffix): ?string
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return implode('.', array_reverse(explode('.', $ip))) . '.' . $v4Suffix;
        }
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $bin = @inet_pton($ip);
            if ($bin === false) {
                return null;
            }
            return implode('.', array_reverse(str_split(bin2hex($bin)))) . '.' . $v6Suffix;
        }
        return null;
    }

    /**
     * Build a standardized unresolved/invalid row payload with consistent schema keys.
     *
     * @return array<string, mixed>
     */
    private function emptyRowResult(
        string $input,
        ?string $host,
        bool $valid,
        string $status,
        ?string $error,
        ?string $cname = null,
        bool $cached = false
    ): array {
        return [
            'input' => $input,
            'host' => $host,
            'valid' => $valid,
            'status' => $status,
            'error' => $error,
            'ipv4' => null,
            'ipv4_all' => [],
            'ipv6' => null,
            'ipv6_all' => [],
            'cname' => $cname,
            'primary_ip' => null,
            'ip_version' => null,
            'ip_version_label' => null,
            'ip_class' => null,
            'ip_scope' => null,
            'class_c' => null,
            'class_c_prefix' => null,
            'class_b' => null,
            'ipv6_subnet_64' => null,
            'ip_type' => self::TYPE_UNRESOLVED,
            'ip_type_label' => self::TYPE_LABELS[self::TYPE_UNRESOLVED],
            'provider' => null,
            'asn' => null,
            'asn_number' => null,
            'bgp_prefix' => null,
            'country' => null,
            'org' => null,
            'rdns' => null,
            'same_ip_count' => 0,
            'is_shared_ip' => false,
            'same_class_c_count' => 0,
            'is_same_class_c' => false,
            'cached' => $cached,
        ];
    }

    /**
     * Parse Team Cymru origin TXT record: "13335 | 1.1.1.0/24 | AU | apnic | 2011-08-11".
     *
     * @return array{asn_number: int, bgp_prefix: ?string, country: ?string}|null
     */
    private function parseCymruOriginTxt(string $raw): ?array
    {
        $clean = $this->unquoteDnsTxt($raw);
        $parts = array_map('trim', explode('|', $clean));
        if (count($parts) < 3) {
            return null;
        }

        // Cymru occasionally returns multi-origin ASNs separated by spaces, e.g. "13335 13336"
        $asnTokens = preg_split('/\s+/', $parts[0]);
        $asnInt = (int) ($asnTokens[0] ?? 0);
        if ($asnInt <= 0) {
            return null;
        }

        return [
            'asn_number' => $asnInt,
            'bgp_prefix' => $parts[1] !== '' ? $parts[1] : null,
            'country' => $parts[2] !== '' ? strtoupper($parts[2]) : null,
        ];
    }

    /**
     * Parse Team Cymru AS TXT record: "13335 | US | arin | 2010-07-14 | CLOUDFLARENET - Cloudflare, Inc., US".
     */
    private function parseCymruAsnTxt(string $raw): ?string
    {
        $clean = $this->unquoteDnsTxt($raw);
        $parts = array_map('trim', explode('|', $clean));
        if (count($parts) < 5 || $parts[4] === '') {
            return null;
        }

        return $this->cleanOrgDisplayName($parts[4]);
    }

    private function unquoteDnsTxt(string $raw): string
    {
        // Cloudflare DoH returns JSON-escaped / RFC 1035 quoted TXT strings like "\"13335 | ...\""
        $clean = trim($raw);
        $clean = str_replace(['" "', '""'], '', $clean);
        return trim($clean, "\"' \t\r\n");
    }

    private function cleanOrgDisplayName(string $org): string
    {
        $trimmed = trim($org);
        // Strip trailing two-letter country code often appended by Team Cymru, e.g. ", US"
        return preg_replace('/,\s*[A-Z]{2}$/', '', $trimmed) ?? $trimmed;
    }

    /**
     * @return array{asn_number: null, asn: null, bgp_prefix: null, country: null, org: null, rdns: null}
     */
    private function emptyIpMetadata(): array
    {
        return [
            'asn_number' => null,
            'asn' => null,
            'bgp_prefix' => null,
            'country' => null,
            'org' => null,
            'rdns' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $raw
     * @return array{asn_number: ?int, asn: ?string, bgp_prefix: ?string, country: ?string, org: ?string, rdns: ?string}
     */
    private function normalizeMetadataPayload(array $raw): array
    {
        $asnNum = isset($raw['asn_number']) && is_numeric($raw['asn_number']) ? (int) $raw['asn_number'] : null;
        $asn = $raw['asn'] ?? ($asnNum !== null && $asnNum > 0 ? "AS{$asnNum}" : null);

        return [
            'asn_number' => $asnNum,
            'asn' => $asn,
            'bgp_prefix' => !empty($raw['bgp_prefix']) ? (string) $raw['bgp_prefix'] : null,
            'country' => !empty($raw['country']) ? strtoupper((string) $raw['country']) : null,
            'org' => !empty($raw['org']) ? (string) $raw['org'] : null,
            'rdns' => !empty($raw['rdns']) ? rtrim(strtolower((string) $raw['rdns']), '.') : null,
        ];
    }

    /**
     * @return array{ip_type: string, ip_type_label: string, provider: ?string}
     */
    private function makeInfraResult(string $type, ?string $provider): array
    {
        return [
            'ip_type' => $type,
            'ip_type_label' => self::TYPE_LABELS[$type] ?? 'Unknown',
            'provider' => $provider,
        ];
    }

    /**
     * @return array{input: string, host: null, is_ip: false, valid: false, error: string}
     */
    private function invalidNormalization(string $input, string $error): array
    {
        return [
            'input' => $input,
            'host' => null,
            'is_ip' => false,
            'valid' => false,
            'error' => $error,
        ];
    }

    /**
     * Avoid DB cache locks during 10K-domain concurrent requests by using file/memory cache instead of database store.
     */
    protected function cacheStore(): ?CacheRepository
    {
        try {
            $default = config('cache.default', 'file');
            if ($default === 'database') {
                return Cache::store('file');
            }
            return Cache::store($default);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function cacheGet(string $key): mixed
    {
        try {
            return $this->cacheStore()?->get($key);
        } catch (\Throwable) {
            return null;
        }
    }

    protected function cachePut(string $key, mixed $value, int $ttlSeconds): void
    {
        try {
            $this->cacheStore()?->put($key, $value, $ttlSeconds);
        } catch (\Throwable) {
            // Silently degrade to in-memory cache if filesystem cache is unavailable
        }
    }
}
