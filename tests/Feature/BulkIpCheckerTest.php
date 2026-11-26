<?php

namespace Tests\Feature;

use App\Http\Controllers\BulkIpCheckerController;
use App\Services\BulkIpCheckerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class BulkIpCheckerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure APP_KEY and array cache exist even in isolated worktree test runs without .env
        if (empty(config('app.key'))) {
            config(['app.key' => 'base64:' . base64_encode(str_repeat('a', 32))]);
        }
        config(['cache.default' => 'array']);
    }

    public function test_routes_are_registered_above_service_slug_route(): void
    {
        $this->assertTrue(Route::has('services.bulk-ip-checker'));
        $this->assertTrue(Route::has('services.bulk-ip-checker.resolve'));

        $this->assertSame('/services/bulk-website-ip-checker', route('services.bulk-ip-checker', [], false));
        $this->assertSame('/services/bulk-website-ip-checker/resolve', route('services.bulk-ip-checker.resolve', [], false));

        $matchedRoute = Route::getRoutes()->match(Request::create('/services/bulk-website-ip-checker', 'GET'));
        $this->assertStringContainsString(BulkIpCheckerController::class, $matchedRoute->getActionName());

        if (view()->exists('frontend.services.bulk-ip-checker')) {
            try {
                $response = $this->get(route('services.bulk-ip-checker'));
                $response->assertStatus(200);
            } catch (\Throwable $e) {
                // Skip full DB-backed layout rendering only if test DB is unmigrated/offline
                if (!str_contains($e->getMessage(), 'SQLSTATE')) {
                    throw $e;
                }
            }
        }
    }

    public function test_url_and_domain_normalization_handles_urls_ports_paths_ips_and_invalid_inputs(): void
    {
        $service = new BulkIpCheckerService();

        $cases = [
            ['https://EXAMPLE.COM:8443/path/to/page?foo=bar#top', 'example.com', false, true],
            ['http://sub.domain.co.uk/', 'sub.domain.co.uk', false, true],
            ['//cdn.example.org/asset.js', 'cdn.example.org', false, true],
            ['user:pass@portal.example.com:8080/login', 'portal.example.com', false, true],
            ['  trailing.example.com.  ', 'trailing.example.com', false, true],
            ['192.168.1.50:8080', '192.168.1.50', true, true],
            ['[2606:4700:4700::1111]:443/dns-query', '2606:4700:4700::1111', true, true],
            ['', null, false, false],
            ['   ', null, false, false],
            ['not a valid domain', null, false, false],
            ['javascript:alert(1)', null, false, false],
            ['mailto:admin@example.com', null, false, false],
            ['foo..bar.com', null, false, false],
            ['-badhost.com', null, false, false],
            ['999.999.999.999', null, false, false],
        ];

        foreach ($cases as [$raw, $expectedHost, $expectedIsIp, $expectedValid]) {
            $result = $service->normalizeInput($raw);
            $this->assertSame($expectedValid, $result['valid'], "Failed valid check for: {$raw}");
            $this->assertSame($expectedHost, $result['host'], "Failed host check for: {$raw}");
            $this->assertSame($expectedIsIp, $result['is_ip'], "Failed is_ip check for: {$raw}");
        }
    }

    public function test_ipv4_classful_block_and_special_scope_classification(): void
    {
        $service = new BulkIpCheckerService();

        $aPrivate = $service->classifyIpBlock('10.10.1.5');
        $this->assertSame(4, $aPrivate['ip_version']);
        $this->assertSame('Class A', $aPrivate['ip_class']);
        $this->assertSame('Private (RFC1918)', $aPrivate['ip_scope']);
        $this->assertSame('10.10.1.0/24', $aPrivate['class_c']);
        $this->assertSame('10.10.0.0/16', $aPrivate['class_b']);
        $this->assertTrue($aPrivate['is_private_or_reserved']);

        $bPrivate = $service->classifyIpBlock('172.16.25.4');
        $this->assertSame('Class B', $bPrivate['ip_class']);
        $this->assertSame('Private (RFC1918)', $bPrivate['ip_scope']);
        $this->assertTrue($bPrivate['is_private_or_reserved']);

        $cPrivate = $service->classifyIpBlock('192.168.100.42');
        $this->assertSame('Class C', $cPrivate['ip_class']);
        $this->assertSame('Private (RFC1918)', $cPrivate['ip_scope']);
        $this->assertSame('192.168.100.0/24', $cPrivate['class_c']);
        $this->assertTrue($cPrivate['is_private_or_reserved']);

        $loopback = $service->classifyIpBlock('127.0.0.1');
        $this->assertSame('Class A', $loopback['ip_class']);
        $this->assertSame('Loopback (RFC1122)', $loopback['ip_scope']);
        $this->assertTrue($loopback['is_private_or_reserved']);

        $cgnat = $service->classifyIpBlock('100.64.5.5');
        $this->assertSame('Class A', $cgnat['ip_class']);
        $this->assertSame('CGNAT (RFC6598)', $cgnat['ip_scope']);
        $this->assertTrue($cgnat['is_private_or_reserved']);

        $multicast = $service->classifyIpBlock('224.0.0.1');
        $this->assertSame('Class D (Multicast)', $multicast['ip_class']);
        $this->assertTrue($multicast['is_private_or_reserved']);

        $reserved = $service->classifyIpBlock('240.0.0.1');
        $this->assertSame('Class E (Reserved)', $reserved['ip_class']);
        $this->assertTrue($reserved['is_private_or_reserved']);

        $publicB = $service->classifyIpBlock('185.151.30.189');
        $this->assertSame('Class B', $publicB['ip_class']);
        $this->assertSame('Public', $publicB['ip_scope']);
        $this->assertFalse($publicB['is_private_or_reserved']);
    }

    public function test_ipv6_classification_and_subnet_masking(): void
    {
        $service = new BulkIpCheckerService();

        $global = $service->classifyIpBlock('2001:4860:4860::8888');
        $this->assertSame(6, $global['ip_version']);
        $this->assertSame('IPv6', $global['ip_class']);
        $this->assertSame('IPv6 Global Unicast', $global['ip_scope']);
        $this->assertSame('2001:4860:4860::/64', $global['ipv6_subnet_64']);
        $this->assertSame('2001:4860:4860::/48', $global['ipv6_subnet_48']);
        $this->assertFalse($global['is_private_or_reserved']);

        $loopback = $service->classifyIpBlock('::1');
        $this->assertSame('IPv6 Loopback', $loopback['ip_scope']);
        $this->assertTrue($loopback['is_private_or_reserved']);

        $ula = $service->classifyIpBlock('fd12:3456:789a::1');
        $this->assertSame('IPv6 Unique Local (ULA)', $ula['ip_scope']);
        $this->assertTrue($ula['is_private_or_reserved']);
    }

    public function test_infrastructure_ip_type_classification_for_cdn_cloud_datacenter_private_and_hosting(): void
    {
        $service = new BulkIpCheckerService();

        $cf = $service->classifyInfrastructure('104.16.132.229', $service->classifyIpBlock('104.16.132.229'));
        $this->assertSame(BulkIpCheckerService::TYPE_CDN_PROXY, $cf['ip_type']);
        $this->assertSame('Cloudflare', $cf['provider']);

        $stackCdn = $service->classifyInfrastructure('185.151.30.189', $service->classifyIpBlock('185.151.30.189'));
        $this->assertSame(BulkIpCheckerService::TYPE_CDN_PROXY, $stackCdn['ip_type']);
        $this->assertSame('20i / StackCDN', $stackCdn['provider']);

        $cloudfront = $service->classifyInfrastructure(
            '13.35.10.20',
            $service->classifyIpBlock('13.35.10.20'),
            ['asn_number' => 16509, 'org' => 'AMAZON-02'],
            'd111111abcdef8.cloudfront.net'
        );
        $this->assertSame(BulkIpCheckerService::TYPE_CDN_PROXY, $cloudfront['ip_type']);
        $this->assertSame('Amazon CloudFront', $cloudfront['provider']);

        $aws = $service->classifyInfrastructure(
            '54.160.1.2',
            $service->classifyIpBlock('54.160.1.2'),
            ['asn_number' => 16509, 'org' => 'AMAZON-02 - Amazon.com, Inc.']
        );
        $this->assertSame(BulkIpCheckerService::TYPE_CLOUD, $aws['ip_type']);
        $this->assertSame('Amazon AWS', $aws['provider']);

        $do = $service->classifyInfrastructure('134.209.10.20', $service->classifyIpBlock('134.209.10.20'));
        $this->assertSame(BulkIpCheckerService::TYPE_DATACENTER, $do['ip_type']);
        $this->assertSame('DigitalOcean', $do['provider']);

        $hetzner = $service->classifyInfrastructure(
            '213.133.116.44',
            $service->classifyIpBlock('213.133.116.44'),
            ['asn_number' => 24940, 'org' => 'HETZNER-AS - Hetzner Online GmbH']
        );
        $this->assertSame(BulkIpCheckerService::TYPE_DATACENTER, $hetzner['ip_type']);
        $this->assertSame('Hetzner', $hetzner['provider']);

        $isp = $service->classifyInfrastructure(
            '117.198.10.5',
            $service->classifyIpBlock('117.198.10.5'),
            ['asn_number' => 9829, 'org' => 'NIB-INDIA-AS National Internet Backbone, IN']
        );
        $this->assertSame(BulkIpCheckerService::TYPE_HOSTING_ISP, $isp['ip_type']);

        $priv = $service->classifyInfrastructure('192.168.1.1', $service->classifyIpBlock('192.168.1.1'));
        $this->assertSame(BulkIpCheckerService::TYPE_PRIVATE_BOGON, $priv['ip_type']);
    }

    public function test_batch_resolve_api_endpoint_schema_and_shared_ip_grouping_with_mocked_dns(): void
    {
        $service = (new BulkIpCheckerService())
            ->setDnsResolver(function (array $hosts): array {
                $map = [
                    'shrawaneffects.com' => [
                        'ipv4' => ['185.151.30.189'],
                        'ipv6' => ['2a07:7800::189'],
                        'cname' => null,
                    ],
                    'www.shrawaneffects.com' => [
                        'ipv4' => ['185.151.30.189'],
                        'ipv6' => ['2a07:7800::189'],
                        'cname' => null,
                    ],
                    'sibling.example.com' => [
                        'ipv4' => ['185.151.30.200'],
                        'ipv6' => [],
                        'cname' => null,
                    ],
                    'cf-site.example.com' => [
                        'ipv4' => ['104.16.132.229'],
                        'ipv6' => ['2606:4700::6810:85e5'],
                        'cname' => null,
                    ],
                    'ipv6only.example.com' => [
                        'ipv4' => [],
                        'ipv6' => ['2001:4860:4860::8888'],
                        'cname' => null,
                    ],
                    'missing.invalid' => [
                        'ipv4' => [],
                        'ipv6' => [],
                        'cname' => null,
                    ],
                ];

                $out = [];
                foreach ($hosts as $h) {
                    $out[$h] = $map[$h] ?? ['ipv4' => [], 'ipv6' => [], 'cname' => null];
                }
                return $out;
            })
            ->setIpMetadataResolver(function (array $ips): array {
                $meta = [
                    '185.151.30.189' => [
                        'asn_number' => 48254,
                        'bgp_prefix' => '185.151.30.0/24',
                        'country' => 'GB',
                        'org' => 'TWENTYI - 20i Limited',
                        'rdns' => '185-151-30-189.ptr4.stackcp.net',
                    ],
                    '185.151.30.200' => [
                        'asn_number' => 48254,
                        'bgp_prefix' => '185.151.30.0/24',
                        'country' => 'GB',
                        'org' => 'TWENTYI - 20i Limited',
                        'rdns' => '185-151-30-200.ptr4.stackcp.net',
                    ],
                    '104.16.132.229' => [
                        'asn_number' => 13335,
                        'bgp_prefix' => '104.16.128.0/20',
                        'country' => 'US',
                        'org' => 'CLOUDFLARENET - Cloudflare, Inc.',
                        'rdns' => null,
                    ],
                    '2001:4860:4860::8888' => [
                        'asn_number' => 15169,
                        'bgp_prefix' => '2001:4860::/32',
                        'country' => 'US',
                        'org' => 'GOOGLE - Google LLC',
                        'rdns' => 'dns.google',
                    ],
                ];

                $out = [];
                foreach ($ips as $ip) {
                    $out[$ip] = $meta[$ip] ?? [];
                }
                return $out;
            });

        $this->app->instance(BulkIpCheckerService::class, $service);

        $response = $this->postJson(route('services.bulk-ip-checker.resolve'), [
            'domains' => [
                'https://shrawaneffects.com/services',
                'www.shrawaneffects.com',
                'sibling.example.com',
                'cf-site.example.com',
                'ipv6only.example.com',
                'missing.invalid',
                'javascript:alert(1)',
            ],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'meta' => [
                    'submitted_count',
                    'processed_count',
                    'unique_hosts_count',
                    'resolved_count',
                    'unresolved_count',
                    'invalid_count',
                    'shared_ip_groups_count',
                    'shared_class_c_groups_count',
                    'elapsed_ms',
                ],
                'results' => [
                    '*' => [
                        'input', 'host', 'valid', 'status', 'error',
                        'ipv4', 'ipv4_all', 'ipv6', 'ipv6_all', 'cname', 'primary_ip',
                        'ip_version', 'ip_version_label', 'ip_class', 'ip_scope',
                        'class_c', 'class_c_prefix', 'class_b', 'ipv6_subnet_64',
                        'ip_type', 'ip_type_label', 'provider',
                        'asn', 'asn_number', 'bgp_prefix', 'country', 'org', 'rdns',
                        'same_ip_count', 'is_shared_ip', 'same_class_c_count', 'is_same_class_c',
                        'cached',
                    ],
                ],
                'clusters' => [
                    'by_ip',
                    'by_class_c',
                ],
            ]);

        $payload = $response->json();
        $this->assertTrue($payload['success']);
        $this->assertSame(7, $payload['meta']['submitted_count']);
        $this->assertSame(5, $payload['meta']['resolved_count']);
        $this->assertSame(1, $payload['meta']['unresolved_count']);
        $this->assertSame(1, $payload['meta']['invalid_count']);
        $this->assertSame(1, $payload['meta']['shared_ip_groups_count']);
        $this->assertSame(1, $payload['meta']['shared_class_c_groups_count']);

        // shrawaneffects.com & www.shrawaneffects.com share 185.151.30.189 (2 sites) and Class C 185.151.30.0/24 (3 sites)
        $first = $payload['results'][0];
        $this->assertSame('shrawaneffects.com', $first['host']);
        $this->assertSame('185.151.30.189', $first['ipv4']);
        $this->assertSame('2a07:7800::189', $first['ipv6']);
        $this->assertSame('cdn_proxy', $first['ip_type']);
        $this->assertSame('20i / StackCDN', $first['provider']);
        $this->assertSame('AS48254', $first['asn']);
        $this->assertSame(2, $first['same_ip_count']);
        $this->assertTrue($first['is_shared_ip']);
        $this->assertSame(3, $first['same_class_c_count']);
        $this->assertTrue($first['is_same_class_c']);

        // cf-site is unique on 104.16.132.229
        $cf = $payload['results'][3];
        $this->assertSame(1, $cf['same_ip_count']);
        $this->assertFalse($cf['is_shared_ip']);

        // ipv6only resolves cleanly with primary_ip = IPv6
        $v6Only = $payload['results'][4];
        $this->assertSame('resolved', $v6Only['status']);
        $this->assertSame(6, $v6Only['ip_version']);
        $this->assertSame('2001:4860:4860::8888', $v6Only['primary_ip']);
        $this->assertSame('2001:4860:4860::/64', $v6Only['ipv6_subnet_64']);
        $this->assertSame('cloud', $v6Only['ip_type']);

        // Unresolved and invalid entries must NEVER be falsely flagged as shared IP / same Class C
        $unresolved = $payload['results'][5];
        $this->assertSame('unresolved', $unresolved['status']);
        $this->assertFalse($unresolved['is_shared_ip']);
        $this->assertFalse($unresolved['is_same_class_c']);
        $this->assertSame(0, $unresolved['same_ip_count']);
        $this->assertSame(0, $unresolved['same_class_c_count']);

        $invalid = $payload['results'][6];
        $this->assertSame('invalid', $invalid['status']);
        $this->assertFalse($invalid['is_shared_ip']);
        $this->assertFalse($invalid['is_same_class_c']);

        // Shared cluster assertions
        $this->assertCount(1, $payload['clusters']['by_ip']);
        $this->assertSame('185.151.30.189', $payload['clusters']['by_ip'][0]['ip']);
        $this->assertSame(['shrawaneffects.com', 'www.shrawaneffects.com'], $payload['clusters']['by_ip'][0]['hosts']);

        $this->assertCount(1, $payload['clusters']['by_class_c']);
        $this->assertSame('185.151.30.0/24', $payload['clusters']['by_class_c'][0]['class_c']);
        $this->assertSame(3, $payload['clusters']['by_class_c'][0]['count']);
    }

    public function test_batch_resolve_validation_rejects_empty_and_oversized_batches(): void
    {
        $this->postJson(route('services.bulk-ip-checker.resolve'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['domains']);

        $this->postJson(route('services.bulk-ip-checker.resolve'), ['domains' => []])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['domains']);

        $oversized = array_fill(0, BulkIpCheckerService::MAX_BATCH_SIZE + 1, 'example.com');
        $this->postJson(route('services.bulk-ip-checker.resolve'), ['domains' => $oversized])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['domains']);
    }

    public function test_deduplicate_option_and_caching_avoid_duplicate_lookups(): void
    {
        $dnsCallHosts = [];
        $metaCallIps = [];

        $service = (new BulkIpCheckerService())
            ->setDnsResolver(function (array $hosts) use (&$dnsCallHosts): array {
                $dnsCallHosts[] = $hosts;
                $out = [];
                foreach ($hosts as $h) {
                    $out[$h] = ['ipv4' => ['93.184.216.34'], 'ipv6' => [], 'cname' => null];
                }
                return $out;
            })
            ->setIpMetadataResolver(function (array $ips) use (&$metaCallIps): array {
                $metaCallIps[] = $ips;
                $out = [];
                foreach ($ips as $ip) {
                    $out[$ip] = ['asn_number' => 15133, 'bgp_prefix' => '93.184.216.0/24', 'country' => 'US', 'org' => 'EDGECAST'];
                }
                return $out;
            });

        // Two URLs resolving to same normalized host -> DNS queried only once, 2 rows returned when deduplicate=false
        $batch1 = $service->resolveBatch([
            'https://example.com/page-1',
            'http://EXAMPLE.COM/page-2',
        ], deduplicate: false);

        $this->assertCount(2, $batch1['results']);
        $this->assertSame([['example.com']], $dnsCallHosts);
        $this->assertSame([['93.184.216.34']], $metaCallIps);

        // Second call with deduplicate=true hits cache (zero new resolver calls) and returns 1 row
        $batch2 = $service->resolveBatch([
            'example.com',
            'https://example.com/other',
        ], deduplicate: true);

        $this->assertCount(1, $batch2['results']);
        $this->assertTrue($batch2['results'][0]['cached']);
        $this->assertCount(1, $dnsCallHosts);
        $this->assertCount(1, $metaCallIps);
    }

    public function test_failed_asn_lookup_does_not_poison_persistent_cache(): void
    {
        $firstCallCount = 0;
        $firstService = (new BulkIpCheckerService())
            ->setDnsResolver(fn (array $hosts): array => [
                'flaky-meta.example' => ['ipv4' => ['8.8.4.4'], 'ipv6' => [], 'cname' => null],
            ])
            ->setIpMetadataResolver(function (array $ips) use (&$firstCallCount): array {
                $firstCallCount++;
                // Simulate transient Cymru outage returning null ASN
                return ['8.8.4.4' => ['asn_number' => null, 'org' => null, 'rdns' => 'dns.google']];
            });

        $res1 = $firstService->resolveBatch(['flaky-meta.example']);
        $this->assertSame(1, $firstCallCount);
        $this->assertNull($res1['results'][0]['asn_number']);

        // A fresh instance (next HTTP batch) must NOT read a poisoned null-ASN cache entry
        $secondCallCount = 0;
        $secondService = (new BulkIpCheckerService())
            ->setDnsResolver(fn (array $hosts): array => [
                'flaky-meta.example' => ['ipv4' => ['8.8.4.4'], 'ipv6' => [], 'cname' => null],
            ])
            ->setIpMetadataResolver(function (array $ips) use (&$secondCallCount): array {
                $secondCallCount++;
                return [
                    '8.8.4.4' => [
                        'asn_number' => 15169,
                        'bgp_prefix' => '8.8.4.0/24',
                        'country' => 'US',
                        'org' => 'GOOGLE - Google LLC',
                        'rdns' => 'dns.google',
                    ],
                ];
            });

        $res2 = $secondService->resolveBatch(['flaky-meta.example']);
        $this->assertSame(1, $secondCallCount);
        $this->assertSame(15169, $res2['results'][0]['asn_number']);
        $this->assertSame('AS15169', $res2['results'][0]['asn']);
        $this->assertSame('GOOGLE - Google LLC', $res2['results'][0]['org']);

        // Third instance should now hit the persistent cache for 8.8.4.4 without calling metadata resolver
        $thirdCallCount = 0;
        $thirdService = (new BulkIpCheckerService())
            ->setIpMetadataResolver(function (array $ips) use (&$thirdCallCount): array {
                $thirdCallCount++;
                return [];
            });

        $res3 = $thirdService->resolveBatch(['8.8.4.4']);
        $this->assertSame(0, $thirdCallCount);
        $this->assertSame(15169, $res3['results'][0]['asn_number']);
    }

    public function test_cymru_doh_failure_falls_back_to_native_dns_txt_for_origin_and_asn_name(): void
    {
        $service = new class extends BulkIpCheckerService {
            public array $nativeTxtCalls = [];

            protected function executeDohWithFailover(array $queries, string $primary = 'cloudflare', string $fallback = 'google'): array
            {
                // Simulate DoH SERVFAIL / EDE(22) throttling on Cymru TXT queries while PTR succeeds
                $out = [];
                foreach ($queries as $q) {
                    if ($q['type'] === 'PTR') {
                        $out[$q['key']] = [
                            'ok' => true,
                            'status' => 0,
                            'answers' => [
                                ['type' => 12, 'data' => '185-151-30-189.ptr4.stackcp.net.'],
                            ],
                        ];
                    } else {
                        $out[$q['key']] = ['ok' => false, 'status' => 2, 'answers' => []];
                    }
                }
                return $out;
            }

            protected function resolveTxtRecordsNative(string $hostname): array
            {
                $this->nativeTxtCalls[] = $hostname;
                return match ($hostname) {
                    '189.30.151.185.origin.asn.cymru.com' => ['48254 | 185.151.30.0/24 | GB | ripencc | 2016-05-06'],
                    'AS48254.asn.cymru.com' => ['48254 | GB | ripencc | 2018-09-20 | TWENTYI - 20i Limited, GB'],
                    default => [],
                };
            }
        };

        $result = $service->resolveBatch(['185.151.30.189']);
        $row = $result['results'][0];

        $this->assertSame('resolved', $row['status']);
        $this->assertSame(48254, $row['asn_number']);
        $this->assertSame('AS48254', $row['asn']);
        $this->assertSame('185.151.30.0/24', $row['bgp_prefix']);
        $this->assertSame('GB', $row['country']);
        $this->assertSame('TWENTYI - 20i Limited', $row['org']);
        $this->assertSame('185-151-30-189.ptr4.stackcp.net', $row['rdns']);
        $this->assertContains('189.30.151.185.origin.asn.cymru.com', $service->nativeTxtCalls);
        $this->assertContains('AS48254.asn.cymru.com', $service->nativeTxtCalls);
    }
}
