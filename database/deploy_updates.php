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
} catch (\Throwable $e) {
    echo "Note: " . $e->getMessage() . "\n";
}
