<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Update canonical URLs in post_seo to have /blog/
    $posts = Illuminate\Support\Facades\DB::table("posts")->pluck("slug");
    $countSeo = 0;
    foreach ($posts as $slug) {
        $countSeo += Illuminate\Support\Facades\DB::table("post_seo")
            ->where("canonical_url", "https://shrawaneffects.com/" . $slug)
            ->update([
                "canonical_url" => "https://shrawaneffects.com/blog/" . $slug
            ]);
    }

    // 2. Update post content internal links to have /blog/
    foreach ($posts as $slug) {
        Illuminate\Support\Facades\DB::table("posts")
            ->where("content", "LIKE", "%https://shrawaneffects.com/" . $slug . "%")
            ->where("content", "NOT LIKE", "%https://shrawaneffects.com/blog/" . $slug . "%")
            ->update([
                "content" => Illuminate\Support\Facades\DB::raw("REPLACE(content, 'https://shrawaneffects.com/" . $slug . "', 'https://shrawaneffects.com/blog/" . $slug . "')")
            ]);
    }
    echo "Updated $countSeo canonical URLs to /blog/ in database.\n";

    // 3. Promote headings for vivo-x500-series
    $vivoPost = Illuminate\Support\Facades\DB::table("posts")->where("slug", "vivo-x500-series")->first();
    if ($vivoPost) {
        $updated = preg_replace('/<h3(\s+[^>]*)?>/i', '<h2$1>', $vivoPost->content);
        $updated = preg_replace('/<\/h3>/i', '</h2>', $updated);
        $updated = preg_replace('/<h4(\s+[^>]*)?>/i', '<h3$1>', $updated);
        $updated = preg_replace('/<\/h4>/i', '</h3>', $updated);
        Illuminate\Support\Facades\DB::table("posts")->where("id", $vivoPost->id)->update(["content" => $updated]);
        echo "Promoted vivo-x500-series headings to semantic H2 and H3.\n";
    }

    // 4. Ensure tools table exists
    if (!Illuminate\Support\Facades\Schema::hasTable('tools')) {
        Illuminate\Support\Facades\Schema::create('tools', function (\Illuminate\Database\Schema\Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('url')->nullable();
            $table->string('route_name')->nullable();
            $table->string('category', 100)->nullable()->default('Utilities');
            $table->string('icon', 100)->nullable()->default('bi-tools');
            $table->string('badge', 50)->nullable();
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->integer('order')->default(0);
            $table->string('type', 30)->default('frontend');
            $table->timestamps();
        });
        echo "Created 'tools' table.\n";
    }

    // 5. Seed or Update Rich Content for Default Tools
    $bulkIpContent = <<<'HTML'
<div class="tool-guide-wrapper">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 p-4 rounded-4 bg-body border-0 shadow-sm" style="border: 1px solid rgba(0, 255, 102, 0.2) !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-4">
                        <i class="bi bi-lightning-charge-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">High-Concurrency Resolving</h5>
                </div>
                <p class="text-body-secondary mb-0">Resolves up to 10,000+ domains simultaneously using asynchronous non-blocking DNS pipelines with auto-batching.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4 rounded-4 bg-body border-0 shadow-sm" style="border: 1px solid rgba(0, 240, 255, 0.2) !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-4">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Subnet & PBN Clustering</h5>
                </div>
                <p class="text-body-secondary mb-0">Automatically clusters domains by Exact Same IP and Class-C (/24) network blocks to uncover shared hosting neighbors.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4 rounded-4 bg-body border-0 shadow-sm" style="border: 1px solid rgba(255, 193, 7, 0.2) !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-4">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5 class="fw-bold mb-0">ASN & Provider Detection</h5>
                </div>
                <p class="text-body-secondary mb-0">Accurately identifies Cloudflare, CloudFront, Fastly, AWS, Google Cloud, DigitalOcean, and dedicated hosting providers.</p>
            </div>
        </div>
    </div>

    <h2>Understanding Bulk IP & Network DNS Resolution</h2>
    <p>Every website accessible on the public Internet relies on the Domain Name System (DNS) to map human-readable domain names into machine-routable Internet Protocol (IP) addresses. In large-scale SEO audits, cybersecurity threat hunting, server migrations, and competitor research, checking domain IPs individually is tedious and slow. The <strong>Shrawan Effects Bulk Website IP Checker</strong> provides a high-throughput, asynchronous pipeline engineered to resolve thousands of domains in seconds.</p>

    <h3>How the Asynchronous Resolution Engine Works</h3>
    <p>Unlike standard sequential lookups that block execution for every query, our architecture leverages concurrent DNS worker queues:</p>
    <ul>
        <li><strong>Domain & URL Sanitization:</strong> Automatically extracts valid hostnames from raw URLs, removing protocols (<code>http://</code>, <code>https://</code>), port numbers, paths, and query parameters.</li>
        <li><strong>A & AAAA Record Resolution:</strong> Queries authoritative nameservers for both standard 32-bit IPv4 addresses and 128-bit next-generation IPv6 addresses.</li>
        <li><strong>Autonomous System Number (ASN) Classification:</strong> Resolves the BGP routing origin and Autonomous System name to classify whether the destination is behind a CDN proxy, a cloud hyperscaler, or a bare-metal datacenter.</li>
        <li><strong>Canonical Reverse PTR Lookup:</strong> Verifies the PTR record to validate server authenticity and detect co-located virtual host configurations.</li>
    </ul>

    <h2>Class-C Subnet Clustering & Shared Hosting Footprints</h2>
    <p>An IPv4 address consists of four 8-bit octets formatted as <code>A.B.C.D</code> (e.g., <code>192.168.1.100</code>). In network terminology, the first three octets (<code>A.B.C</code>) represent the <strong>Class-C network block</strong> (or <code>/24</code> CIDR subnet), which accommodates up to 254 usable host addresses on the same local broadcast domain.</p>

    <div class="table-responsive my-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Domain Example</th>
                    <th>Resolved IPv4</th>
                    <th>Class-C (/24) Subnet</th>
                    <th>Hosting Footprint Assessment</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>site-one.com</code></td>
                    <td><code>104.21.45.12</code></td>
                    <td><code>104.21.45.0/24</code></td>
                    <td>Cloudflare Anycast CDN (Shared Edge Proxy)</td>
                </tr>
                <tr>
                    <td><code>site-two.com</code></td>
                    <td><code>104.21.45.89</code></td>
                    <td><code>104.21.45.0/24</code></td>
                    <td>Same Class-C Block (Same Cloudflare Edge cluster)</td>
                </tr>
                <tr>
                    <td><code>client-crm.com</code></td>
                    <td><code>159.65.130.45</code></td>
                    <td><code>159.65.130.0/24</code></td>
                    <td>DigitalOcean Datacenter VPS (Standalone Node)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h3>Why Class-C Clustering Matters for SEO & Security</h3>
    <ol>
        <li><strong>Backlink Profile Quality & PBN Auditing:</strong> Search engines like Google evaluate the diversity of referring IP subnets. Multiple backlinks originating from identical Class-C networks often indicate link networks or single-owner hosting, which can trigger algorithmic penalties.</li>
        <li><strong>Co-Tenancy Security Risks:</strong> On shared hosting servers, a compromised neighboring site or a blacklisted IP address due to spam can negatively impact email deliverability and reputation for all other domains sharing that same IP.</li>
        <li><strong>Disaster Recovery & Redundancy:</strong> Validating that critical company domains and APIs do not share single points of failure across identical hosting subnets ensures uninterrupted uptime.</li>
    </ol>

    <h2>Frequently Asked Questions (FAQ)</h2>
    <div class="accordion my-4" id="toolFaqAccordion">
        <div class="card border-0 mb-3 rounded-4 bg-body shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-success"><i class="bi bi-question-circle me-2"></i>What is the maximum number of domains I can check?</h5>
                <p class="mb-0 text-body-secondary">You can paste or upload files containing up to 10,000+ domains at once. The engine processes lookups in concurrent batches (defaulting to 30 domains per batch) to maximize speed while respecting global DNS resolver rate limits.</p>
            </div>
        </div>
        <div class="card border-0 mb-3 rounded-4 bg-body shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-success"><i class="bi bi-question-circle me-2"></i>Can I import domains directly from a CSV or TXT file?</h5>
                <p class="mb-0 text-body-secondary">Yes. Simply click the "IMPORT .TXT / .CSV" button or drag and drop your text file directly into the input area. The tool will parse and deduplicate domain entries automatically.</p>
            </div>
        </div>
        <div class="card border-0 mb-3 rounded-4 bg-body shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-success"><i class="bi bi-question-circle me-2"></i>How do I export my results?</h5>
                <p class="mb-0 text-body-secondary">Once the scan completes, use the "EXPORT CSV" or "EXPORT JSON" buttons to download the full dataset, including hostnames, IPv4, IPv6, ASN details, provider classifications, and subnet clusters.</p>
            </div>
        </div>
    </div>
</div>
HTML;

    $convertCaseContent = <<<'HTML'
<div class="tool-guide-wrapper">
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 p-4 rounded-4 bg-body border-0 shadow-sm" style="border: 1px solid rgba(0, 255, 102, 0.2) !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fs-4">
                        <i class="bi bi-fonts"></i>
                    </div>
                    <h5 class="fw-bold mb-0">7 Distinct Case Types</h5>
                </div>
                <p class="text-body-secondary mb-0">Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe available instantly.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4 rounded-4 bg-body border-0 shadow-sm" style="border: 1px solid rgba(0, 240, 255, 0.2) !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-info bg-opacity-10 text-info rounded-3 fs-4">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Live Real-Time Counters</h5>
                </div>
                <p class="text-body-secondary mb-0">Real-time character count, word count, and line count update automatically with zero page reloads.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 p-4 rounded-4 bg-body border-0 shadow-sm" style="border: 1px solid rgba(255, 193, 7, 0.2) !important;">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="p-3 bg-warning bg-opacity-10 text-warning rounded-3 fs-4">
                        <i class="bi bi-shield-lock-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-0">100% Client-Side Privacy</h5>
                </div>
                <p class="text-body-secondary mb-0">Your text is transformed directly in your browser. Nothing is ever sent to or stored on external servers.</p>
            </div>
        </div>
    </div>

    <h2>Mastering Online Text Transformation & Casing Standards</h2>
    <p>Accurate letter casing is essential for professional communication, digital publishing, database consistency, and code readability. Whether you accidentally left <code>Caps Lock</code> enabled while typing, need to format article titles according to editorial stylebooks, or are standardizing database records, <strong>Shrawan Effects Convert Case</strong> transforms text instantly with zero hassle.</p>

    <h3>Overview of Supported Case Formats</h3>
    <div class="table-responsive my-4">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Case Style</th>
                    <th>Example Output</th>
                    <th>Primary Use Cases & Standards</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Sentence case</strong></td>
                    <td><code>The quick brown fox jumps over the lazy dog.</code></td>
                    <td>Standard English prose, body paragraphs, and email correspondence.</td>
                </tr>
                <tr>
                    <td><strong>lower case</strong></td>
                    <td><code>the quick brown fox jumps over the lazy dog.</code></td>
                    <td>Clean URLs, website slugs, email addresses, and search queries.</td>
                </tr>
                <tr>
                    <td><strong>UPPER CASE</strong></td>
                    <td><code>THE QUICK BROWN FOX JUMPS OVER THE LAZY DOG.</code></td>
                    <td>Legal disclaimers, urgent notifications, acronyms, and bold headings.</td>
                </tr>
                <tr>
                    <td><strong>Capitalized Case</strong></td>
                    <td><code>The Quick Brown Fox Jumps Over The Lazy Dog.</code></td>
                    <td>Proper nouns, customer name directories, and navigation menus.</td>
                </tr>
                <tr>
                    <td><strong>Title Case</strong></td>
                    <td><code>The Quick Brown Fox Jumps over the Lazy Dog.</code></td>
                    <td>Blog post titles, book headlines, and YouTube video headings (AP / Chicago style).</td>
                </tr>
                <tr>
                    <td><strong>aLtErNaTiNg cAsE</strong></td>
                    <td><code>tHe qUiCk bRoWn fOx jUmPs oVeR ThE LaZy dOg.</code></td>
                    <td>Creative social media posts, sarcasm memes, and attention-grabbing tags.</td>
                </tr>
                <tr>
                    <td><strong>InVeRsE CaSe</strong></td>
                    <td><code>tHE QUICK BROWN FOX JUMPS OVER THE LAZY DOG.</code></td>
                    <td>Quickly fixes text typed while Caps Lock was accidentally switched on.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h2>Advanced Productivity Features</h2>
    <ul>
        <li><strong>One-Click Clipboard Copy:</strong> Click the copy icon to transfer transformed text straight to your clipboard with visual confirmation.</li>
        <li><strong>Instant TXT Download:</strong> Save your formatted text locally as a clean <code>.txt</code> file without needing an external text editor.</li>
        <li><strong>Undo & Redo History:</strong> Full keyboard shortcut support (<code>Ctrl + Z</code> to undo, <code>Ctrl + Y</code> to redo) allows you to step back through any changes effortlessly.</li>
        <li><strong>Live Character & Word Counters:</strong> Track character counts (including and excluding spaces), words, and total lines in real time as you type.</li>
    </ul>

    <h2>Frequently Asked Questions (FAQ)</h2>
    <div class="accordion my-4" id="convertCaseFaq">
        <div class="card border-0 mb-3 rounded-4 bg-body shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-success"><i class="bi bi-question-circle me-2"></i>Is my content kept private and secure?</h5>
                <p class="mb-0 text-body-secondary">Yes, absolutely. The Convert Case tool operates 100% within your client browser using vanilla JavaScript. No text is transmitted over the network or saved to any database.</p>
            </div>
        </div>
        <div class="card border-0 mb-3 rounded-4 bg-body shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-success"><i class="bi bi-question-circle me-2"></i>How does Title Case decide which words to capitalize?</h5>
                <p class="mb-0 text-body-secondary">Our algorithm follows standardized Chicago and AP editorial guidelines. Principal nouns, verbs, adjectives, and adverbs are capitalized, while minor coordinating conjunctions, prepositions, and articles (such as <em>a, an, the, and, in, on, of, for, to</em>) remain lowercase unless positioned at the beginning or end of the title.</p>
            </div>
        </div>
        <div class="card border-0 mb-3 rounded-4 bg-body shadow-sm">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-2 text-success"><i class="bi bi-question-circle me-2"></i>Does it support international characters and accents?</h5>
                <p class="mb-0 text-body-secondary">Yes. The engine uses modern Unicode regex patterns (<code>\p{L}</code>), providing full support for accented letters, umlauts, Spanish ñ, French accents, German characters, and non-Latin alphabets.</p>
            </div>
        </div>
    </div>
</div>
HTML;

    // Upsert Bulk IP Checker
    $existingBulk = Illuminate\Support\Facades\DB::table('tools')->where('slug', 'bulk-website-ip-checker')->first();
    if ($existingBulk) {
        if (empty($existingBulk->content)) {
            Illuminate\Support\Facades\DB::table('tools')->where('id', $existingBulk->id)->update([
                'content' => $bulkIpContent,
                'meta_title' => 'Bulk Website IP Checker & Subnet Clustering Tool | Shrawan Effects',
                'meta_description' => 'Resolve up to 10,000+ domains to IPv4 & IPv6 addresses instantly. Detect Cloudflare/CDN proxies, datacenter hosting, and Class-C network footprints.',
                'canonical_url' => 'https://shrawaneffects.com/services/bulk-website-ip-checker',
                'updated_at' => now(),
            ]);
            echo "Updated content for 'bulk-website-ip-checker'.\n";
        }
    } else {
        Illuminate\Support\Facades\DB::table('tools')->insert([
            'title' => 'Bulk Website IP Checker & Subnet Analyzer',
            'slug' => 'bulk-website-ip-checker',
            'url' => '/services/bulk-website-ip-checker',
            'route_name' => 'services.bulk-ip-checker',
            'category' => 'Network & Infrastructure',
            'icon' => 'bi-hdd-network',
            'badge' => 'POPULAR',
            'summary' => 'Asynchronously resolves up to 10,000+ domains into IPv4/IPv6, classifies IP types (CDN, Cloud, Datacenter, ISP), and detects same-IP and Class-C shared hosting footprints.',
            'content' => $bulkIpContent,
            'meta_title' => 'Bulk Website IP Checker & Subnet Clustering Tool | Shrawan Effects',
            'meta_description' => 'Resolve up to 10,000+ domains to IPv4 & IPv6 addresses instantly. Detect Cloudflare/CDN proxies, datacenter hosting, and Class-C network footprints.',
            'canonical_url' => 'https://shrawaneffects.com/services/bulk-website-ip-checker',
            'is_active' => 1,
            'is_featured' => 1,
            'order' => 1,
            'type' => 'frontend',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Inserted 'bulk-website-ip-checker' tool.\n";
    }

    // Upsert Convert Case
    $existingCc = Illuminate\Support\Facades\DB::table('tools')->where('slug', 'convertcase')->first();
    if ($existingCc) {
        if (empty($existingCc->content)) {
            Illuminate\Support\Facades\DB::table('tools')->where('id', $existingCc->id)->update([
                'content' => $convertCaseContent,
                'meta_title' => 'Convert Case - Online Text Case Converter | Shrawan Effects',
                'meta_description' => 'Free online Convert Case tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
                'canonical_url' => 'https://shrawaneffects.com/services/convertcase',
                'updated_at' => now(),
            ]);
            echo "Updated content for 'convertcase'.\n";
        }
    } else {
        Illuminate\Support\Facades\DB::table('tools')->insert([
            'title' => 'Convert Case - Online Text Case Converter',
            'slug' => 'convertcase',
            'url' => '/services/convertcase',
            'route_name' => 'services.convertcase',
            'category' => 'Text & Content Utilities',
            'icon' => 'bi-type',
            'badge' => 'NEW',
            'summary' => 'Instantly transforms text into Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'content' => $convertCaseContent,
            'meta_title' => 'Convert Case - Online Text Case Converter | Shrawan Effects',
            'meta_description' => 'Free online Convert Case tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'canonical_url' => 'https://shrawaneffects.com/services/convertcase',
            'is_active' => 1,
            'is_featured' => 1,
            'order' => 2,
            'type' => 'frontend',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Inserted 'convertcase' tool.\n";
    }

} catch (\Throwable $e) {
    echo "Note: " . $e->getMessage() . "\n";
}
