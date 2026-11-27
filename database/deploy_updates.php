<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "=== Running Database Deploy Updates ===\n";

try {
    // 1. Update canonical URLs in post_seo to have /blog/
    if (Schema::hasTable('posts') && Schema::hasTable('post_seo')) {
        $posts = DB::table("posts")->pluck("slug");
        $countSeo = 0;
        foreach ($posts as $slug) {
            $countSeo += DB::table("post_seo")
                ->where("canonical_url", "https://shrawaneffects.com/" . $slug)
                ->update([
                    "canonical_url" => "https://shrawaneffects.com/blog/" . $slug
                ]);
        }

        // 2. Update post content internal links to have /blog/
        foreach ($posts as $slug) {
            DB::table("posts")
                ->where("content", "LIKE", "%https://shrawaneffects.com/" . $slug . "%")
                ->where("content", "NOT LIKE", "%https://shrawaneffects.com/blog/" . $slug . "%")
                ->update([
                    "content" => DB::raw("REPLACE(content, 'https://shrawaneffects.com/" . $slug . "', 'https://shrawaneffects.com/blog/" . $slug . "')")
                ]);
        }
        echo "Updated $countSeo canonical URLs to /blog/ in database.\n";
    }

    // 3. Promote headings for vivo-x500-series
    if (Schema::hasTable('posts')) {
        $vivoPost = DB::table("posts")->where("slug", "vivo-x500-series")->first();
        if ($vivoPost) {
            $updated = preg_replace('/<h3(\s+[^>]*)?>/i', '<h2$1>', $vivoPost->content);
            $updated = preg_replace('/<\/h3>/i', '</h2>', $updated);
            $updated = preg_replace('/<h4(\s+[^>]*)?>/i', '<h3$1>', $updated);
            $updated = preg_replace('/<\/h4>/i', '</h3>', $updated);
            DB::table("posts")->where("id", $vivoPost->id)->update(["content" => $updated]);
            echo "Promoted vivo-x500-series headings to semantic H2 and H3.\n";
        }
    }

    // 4. Ensure 'tools' table exists
    if (!Schema::hasTable('tools')) {
        Schema::create('tools', function (Blueprint $table) {
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
            $table->boolean('enable_toc')->default(true);
            $table->json('faqs')->nullable();
            $table->json('related_tools')->nullable();
            $table->json('related_posts')->nullable();
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
        echo "Created 'tools' table successfully.\n";
    } else {
        // Ensure all required columns exist
        Schema::table('tools', function (Blueprint $table) {
            if (!Schema::hasColumn('tools', 'enable_toc')) {
                $table->boolean('enable_toc')->default(true)->after('content');
            }
            if (!Schema::hasColumn('tools', 'faqs')) {
                $table->json('faqs')->nullable()->after('enable_toc');
            }
            if (!Schema::hasColumn('tools', 'related_tools')) {
                $table->json('related_tools')->nullable()->after('faqs');
            }
            if (!Schema::hasColumn('tools', 'related_posts')) {
                $table->json('related_posts')->nullable()->after('related_tools');
            }
        });
        echo "Verified 'tools' table columns.\n";
    }

    // 5. Seed / Update Default Public Tools with Rich Guides, TOC Headings & FAQs
    $samplePostIds = [];
    if (Schema::hasTable('posts')) {
        $samplePostIds = DB::table('posts')->where('status', 'published')->limit(4)->pluck('id')->toArray();
    }

    // Tool 1: Bulk Website IP Checker
    $bulkIpContent = <<<HTML
<h2>Understanding High-Scale Bulk IP Resolution and DNS Architecture</h2>
<p>Resolving thousands of hostnames concurrently requires asynchronous DNS queries, low-latency socket pipelines, and robust fallback algorithms. Whether conducting technical SEO site migrations, cybersecurity footprint audits, or competitive infrastructure analysis, discovering where websites are hosted provides crucial architectural intelligence.</p>

<h2>Key Features and Analytical Capabilities</h2>
<p>Our Bulk Website IP Checker provides enterprise-grade infrastructure diagnostics directly in your browser:</p>

<h3>Instant IPv4 and IPv6 Dual-Stack Resolution</h3>
<p>Our engine queries A and AAAA DNS records concurrently to deliver accurate dual-stack addressing for modern web applications and cloud deployments.</p>

<h3>Infrastructure Classification (CDN, Cloud, Datacenter, ISP)</h3>
<p>Autonomous System Numbers (ASN) and IP ownership are cross-referenced to automatically tag cloud hyperscalers (such as Cloudflare, AWS CloudFront, Google Cloud, Fastly, Azure) versus bare-metal VPS hosts and traditional shared hosting servers.</p>

<h3>Same-IP & Class-C (/24) Subnet Cluster Detection</h3>
<p>Detect shared hosting configurations, private blog networks (PBNs), and reverse-proxy footprints across custom domain lists with intelligent grouping by /24 CIDR blocks.</p>

<h2>How to Use the Bulk IP Checker Tool</h2>
<p>1. Paste up to 10,000+ domains or upload a .txt / .csv file into the input area.<br>
2. Toggle deduplication and ASN enrichment according to your audit requirements.<br>
3. Choose your preferred batch size and worker concurrency, then click <strong>RUN BULK IP CHECK</strong>.<br>
4. Inspect live metrics, filter results by status or provider, and export clean CSV reports.</p>

<h2>Technical Specifications & Performance Benchmarks</h2>
<p>Optimized with streaming workers to achieve sub-second response times without blocking the browser thread. Results are processed in stateless batches to maintain zero-footprint privacy and lightning-fast execution.</p>
HTML;

    $bulkIpFaqs = [
        [
            'question' => 'How many domains can I check simultaneously?',
            'answer' => 'You can paste or upload up to 10,000+ domains per session. The browser coordinates batch requests in chunks of 30-50 domains to optimize DNS throughput and prevent network socket throttling.'
        ],
        [
            'question' => 'What is a Class-C (/24) subnet and why does it matter for SEO?',
            'answer' => 'A Class-C subnet represents the first three octets of an IPv4 address (e.g. 192.168.1.x). Websites sharing the same Class-C subnet often reside in the same data center or hosting provider, which helps identify shared hosting footprints and affiliate networks.'
        ],
        [
            'question' => 'Is this tool completely free and private?',
            'answer' => 'Yes, 100% free with no registration or credit card required. Queries are processed in real-time and domain lists are never logged, stored, or shared with third parties.'
        ],
        [
            'question' => 'Can I export the results to Excel or CSV?',
            'answer' => 'Yes! You can instantly export resolved IP addresses, ASN numbers, hosting organizations, and subnet clusters into clean CSV and JSON formats.'
        ]
    ];

    $bulkIpTool = DB::table('tools')->where('slug', 'bulk-website-ip-checker')->first();
    $bulkIpData = [
        'title' => 'Bulk Website IP Checker & Subnet Analyzer',
        'slug' => 'bulk-website-ip-checker',
        'url' => '/services/bulk-website-ip-checker',
        'route_name' => 'services.bulk-ip-checker',
        'type' => 'frontend',
        'category' => 'Network & Infrastructure',
        'icon' => 'bi-hdd-network',
        'badge' => 'POPULAR',
        'summary' => 'Asynchronously resolve up to 10,000+ domains into IPv4/IPv6, classify IP types (CDN, Cloud, Datacenter, ISP), and detect same-IP and Class-C shared hosting footprints.',
        'content' => $bulkIpContent,
        'enable_toc' => true,
        'faqs' => json_encode($bulkIpFaqs),
        'related_tools' => json_encode(['convertcase', 'seo-grader']),
        'related_posts' => json_encode($samplePostIds),
        'meta_title' => 'Bulk Website IP Checker & Class-C Subnet Analyzer (10,000+ Domains) - Shrawan Effects',
        'meta_description' => 'Free Bulk Website IP Checker supporting 10,000+ domains. Resolve IPv4 & IPv6, detect CDN/Proxy/Cloud/Hosting IP types, find Same-IP & Class-C (/24) shared hosting footprints, and export CSV/JSON.',
        'canonical_url' => 'https://shrawaneffects.com/services/bulk-website-ip-checker',
        'is_active' => true,
        'is_featured' => true,
        'order' => 1,
        'updated_at' => now(),
    ];

    if ($bulkIpTool) {
        DB::table('tools')->where('id', $bulkIpTool->id)->update($bulkIpData);
        echo "Updated 'bulk-website-ip-checker' with rich guide, TOC, and FAQs.\n";
    } else {
        $bulkIpData['created_at'] = now();
        DB::table('tools')->insert($bulkIpData);
        echo "Created 'bulk-website-ip-checker' with rich guide, TOC, and FAQs.\n";
    }

    // Tool 2: Convert Case
    $convertCaseContent = <<<HTML
<h2>Comprehensive Guide to Text Case Conversion & Capitalization Styles</h2>
<p>Accurate typography and standardized casing are vital for professional writing, code formatting, social media, and SEO headlines. Manually retyping text in different casing styles is tedious and error-prone. Our Convert Case utility provides an instant, intelligent capitalization suite that adapts your prose or code to industry-standard typographic conventions.</p>

<h2>Supported Letter Case Formats & Rules</h2>
<p>Explore the diverse letter transformation options available in this tool:</p>

<h3>1. Sentence case</h3>
<p>Automatically capitalizes the first letter of each sentence while preserving subsequent lowercase characters, properly handling standard sentence delimiters including periods, question marks, and exclamation points.</p>

<h3>2. lower case and UPPER CASE</h3>
<p>Transforms all characters to uniform lowercase or uppercase, ideal for normalizing identifiers, acronyms, SQL queries, or database inputs.</p>

<h3>3. Capitalized Case and Title Case</h3>
<p>Capitalized Case capitalizes the starting letter of every single word. Title Case conforms to major editorial style guides (such as APA, Chicago, and MLA), intelligently maintaining lowercase for short articles, prepositions, and conjunctions.</p>

<h3>4. aLtErNaTiNg cAsE and InVeRsE CaSe</h3>
<p>Generates stylized alternating letter casing for social memes and gaming handles, or flips every uppercase letter to lowercase and vice versa across your entire document.</p>

<h2>Productivity Features: Character, Word & Line Counters</h2>
<p>The integrated status HUD tracks total characters, characters excluding whitespace, total words, and paragraph lines in real time with zero latency.</p>

<h2>Privacy First: 100% Client-Side Processing</h2>
<p>Your text never leaves your browser. All casing transformations are computed locally using ultra-fast JavaScript algorithms, ensuring strict privacy for your confidential documents.</p>
HTML;

    $convertCaseFaqs = [
        [
            'question' => 'Does Convert Case send my text to a server?',
            'answer' => 'No. All conversions happen entirely inside your web browser via client-side JavaScript. Your confidential notes, emails, and code snippets remain 100% private and secure.'
        ],
        [
            'question' => 'What is the difference between Title Case and Capitalized Case?',
            'answer' => "Capitalized Case capitalizes the initial letter of every word without exception. Title Case conforms to editorial style guides by capitalizing major words while keeping short articles, conjunctions, and prepositions (like 'and', 'in', 'of') in lowercase."
        ],
        [
            'question' => 'Can I undo or redo changes?',
            'answer' => 'Yes! The tool includes full Undo (Ctrl+Z) and Redo (Ctrl+Y) history stacks with up to 40 revision steps.'
        ],
        [
            'question' => 'Is there a character limit on the text input?',
            'answer' => 'There is no strict limit. The high-performance editor easily handles hundreds of thousands of words and multiple megabytes of text without stutter.'
        ]
    ];

    $convertCaseTool = DB::table('tools')->where('slug', 'convertcase')->first();
    $convertCaseData = [
        'title' => 'Convert Case (Online Text Case Converter)',
        'slug' => 'convertcase',
        'url' => '/services/convertcase',
        'route_name' => 'services.convertcase',
        'type' => 'frontend',
        'category' => 'Text & Content Utilities',
        'icon' => 'bi-type',
        'badge' => 'NEW',
        'summary' => 'Instantly transforms text into Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
        'content' => $convertCaseContent,
        'enable_toc' => true,
        'faqs' => json_encode($convertCaseFaqs),
        'related_tools' => json_encode(['bulk-website-ip-checker', 'seo-grader']),
        'related_posts' => json_encode($samplePostIds),
        'meta_title' => 'Convert Case - Online Text Case Converter | Shrawan Effects',
        'meta_description' => 'Free online Convert Case tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
        'canonical_url' => 'https://shrawaneffects.com/services/convertcase',
        'is_active' => true,
        'is_featured' => true,
        'order' => 2,
        'updated_at' => now(),
    ];

    if ($convertCaseTool) {
        DB::table('tools')->where('id', $convertCaseTool->id)->update($convertCaseData);
        echo "Updated 'convertcase' with rich guide, TOC, and FAQs.\n";
    } else {
        $convertCaseData['created_at'] = now();
        DB::table('tools')->insert($convertCaseData);
        echo "Created 'convertcase' with rich guide, TOC, and FAQs.\n";
    }

    echo "=== Database Deploy Updates Completed Successfully ===\n";
} catch (\Throwable $e) {
    echo "Note / Error in deploy_updates: " . $e->getMessage() . "\n";
}
