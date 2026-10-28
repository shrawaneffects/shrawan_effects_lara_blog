<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

view()->share('errors', new \Illuminate\Support\ViewErrorBag());

$user = User::first();
if ($user) {
    Auth::login($user);
}

echo "Testing view renders...\n";

// 1. App Layout / Homepage with navigation
$homeResponse = app(\App\Http\Controllers\HomeController::class)->index(new \Illuminate\Http\Request());
$homeHtml = $homeResponse->render();
echo "[PASS] Homepage & Navbar with Useful Tools rendered (" . strlen($homeHtml) . " bytes)\n";

// 2. Business Scraper index
$bsService = app(\App\Services\UsefulTools\BusinessData\BusinessDataScraperService::class);
$scraperHtml = view('frontend.useful-tools.business-scraper.index', [
    'providers' => $bsService->getAvailableProviders(),
    'businesses' => \App\Models\UsefulTools\UsefulToolBusiness::where('user_id', $user?->id ?? 1)->paginate(15),
    'recentJobs' => \App\Models\UsefulTools\UsefulToolScrapingJob::where('user_id', $user?->id ?? 1)->latest()->take(5)->get(),
    'cities' => collect(['Faridabad', 'Delhi']),
    'totalBusinesses' => 0,
    'verifiedContacts' => 0,
    'totalJobs' => 0,
])->render();
echo "[PASS] Business Data Scraper View rendered (" . strlen($scraperHtml) . " bytes)\n";

// 3. SEO Traffic Analyzer index
$trafficService = app(\App\Services\UsefulTools\Seo\TrafficAnalysisService::class);
$seoHtml = view('frontend.useful-tools.seo-traffic.index', [
    'trackedDomains' => collect(),
    'totalTrackedKeywords' => 0,
    'top3Count' => 0,
    'top10Count' => 0,
    'top100Count' => 0,
    'improvedCount' => 0,
    'declinedCount' => 0,
    'totalEstimatedVisits' => 0,
    'providersStatus' => $trafficService->getProvidersStatus(),
    'recentRankings' => collect(),
])->render();
echo "[PASS] SEO & Traffic Analyzer View rendered (" . strlen($seoHtml) . " bytes)\n";

// 4. Domain Detail
$detailHtml = view('frontend.useful-tools.seo-traffic.domain-detail', [
    'cleanDomain' => 'shrawaneffects.com',
    'keywords' => \App\Models\UsefulTools\UsefulToolTrackedKeyword::where('domain', 'shrawaneffects.com')->paginate(20),
    'domainTotalKeywords' => 0,
    'domainTop3' => 0,
    'domainTop10' => 0,
    'domainTop100' => 0,
    'domainUnranked' => 0,
    'trafficSnapshots' => collect(),
    'latestTraffic' => null,
    'competitorAnalysis' => ['competitors' => []],
    'chartKeywords' => collect(),
])->render();
echo "[PASS] Domain Detail View rendered (" . strlen($detailHtml) . " bytes)\n";

// 5. Exports Index
$exportHtml = view('frontend.useful-tools.exports.index', [
    'exports' => \App\Models\UsefulTools\UsefulToolExport::where('user_id', $user?->id ?? 1)->paginate(15),
])->render();
echo "[PASS] Exports Center View rendered (" . strlen($exportHtml) . " bytes)\n";

echo "\nALL 5 VIEWS COMPILED AND RENDERED SUCCESSFULLY WITH ZERO BLADE ERRORS!\n";
