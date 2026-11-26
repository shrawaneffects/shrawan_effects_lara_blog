<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Clean up canonical URLs in post_seo
    $countSeo = Illuminate\Support\Facades\DB::table("post_seo")
        ->where("canonical_url", "LIKE", "%/blog/%")
        ->update([
            "canonical_url" => Illuminate\Support\Facades\DB::raw("REPLACE(canonical_url, '/blog/', '/')")
        ]);

    // 2. Clean up post content internal links
    $countContent = Illuminate\Support\Facades\DB::table("posts")
        ->where("content", "LIKE", "%/blog/%")
        ->update([
            "content" => Illuminate\Support\Facades\DB::raw("REPLACE(REPLACE(content, 'https://shrawaneffects.com/blog/', 'https://shrawaneffects.com/'), 'href=\"/blog/', 'href=\"/')")
        ]);
    echo "Updated $countSeo canonical URLs and $countContent post content internal links in database.\n";

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
} catch (\Throwable $e) {
    echo "Note: " . $e->getMessage() . "\n";
}
