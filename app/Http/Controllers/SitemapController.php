<?php

namespace App\Http\Controllers;

use App\Services\Seo\SeoSitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Return dynamic XML sitemap
     */
    public function index(): Response
    {
        $xml = SeoSitemapService::generateXml();

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex, follow',
        ]);
    }
}
