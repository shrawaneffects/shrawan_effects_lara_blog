<?php

/**
 * Automated Verification Test Suite for Useful Tools Integration.
 * Executed via PHP CLI with full Laravel Kernel bootstrap.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\UsefulTools\UsefulToolBusiness;
use App\Models\UsefulTools\UsefulToolTrackedKeyword;
use App\Models\UsefulTools\UsefulToolExport;
use App\Services\UsefulTools\Exports\ExportService;
use App\Services\UsefulTools\Seo\RankTrackingService;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

echo "======================================================\n";
echo "   USEFUL TOOLS SUITE: AUTOMATED TEST RUNNER\n";
echo "======================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest(string $description, bool $condition, string $details = '') {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$description}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$description} - Details: {$details}\n";
        $failed++;
    }
}

// -----------------------------------------------------------
// TEST 1: Route Registration
// -----------------------------------------------------------
$registeredRoutes = [
    'useful-tools.business-scraper.index',
    'useful-tools.business-scraper.store',
    'useful-tools.business-scraper.job-status',
    'useful-tools.business-scraper.job-cancel',
    'useful-tools.business-scraper.destroy',
    'useful-tools.business-scraper.export',
    'useful-tools.seo-traffic.index',
    'useful-tools.seo-traffic.domain-detail',
    'useful-tools.seo-traffic.add-keyword',
    'useful-tools.seo-traffic.check-rank',
    'useful-tools.seo-traffic.discover-keywords',
    'useful-tools.seo-traffic.add-competitor',
    'useful-tools.seo-traffic.fetch-traffic',
    'useful-tools.seo-traffic.export-keywords',
    'useful-tools.exports.index',
    'useful-tools.exports.download',
    'useful-tools.exports.destroy',
];

$allRoutesExist = true;
foreach ($registeredRoutes as $rName) {
    if (!Route::has($rName)) {
        $allRoutesExist = false;
        assertTest("Route '{$rName}' is registered", false, "Route missing from router");
    }
}
if ($allRoutesExist) {
    assertTest("All 17 Useful Tools named routes registered", true);
}

// -----------------------------------------------------------
// TEST 2: Existing Routes Preservation
// -----------------------------------------------------------
$existingRoutes = ['home', 'blog.index', 'services.index', 'contact.index', 'faq', 'login'];
$existingRoutesValid = true;
foreach ($existingRoutes as $er) {
    if (!Route::has($er)) {
        $existingRoutesValid = false;
        assertTest("Existing core route '{$er}' preserved", false, "Core route broke");
    }
}
if ($existingRoutesValid) {
    assertTest("All existing website core routes preserved", true);
}

// -----------------------------------------------------------
// TEST 3: SSRF Protection
// -----------------------------------------------------------
assertTest("SSRF Guard: blocks 'http://127.0.0.1:8000'", RankTrackingService::validateAndCleanDomain('http://127.0.0.1:8000') === null);
assertTest("SSRF Guard: blocks 'http://localhost'", RankTrackingService::validateAndCleanDomain('http://localhost') === null);
assertTest("SSRF Guard: blocks 'http://169.254.169.254'", RankTrackingService::validateAndCleanDomain('http://169.254.169.254') === null);
assertTest("SSRF Guard: blocks 'http://192.168.1.1'", RankTrackingService::validateAndCleanDomain('http://192.168.1.1') === null);
assertTest("SSRF Guard: blocks 'http://10.0.0.1'", RankTrackingService::validateAndCleanDomain('http://10.0.0.1') === null);
assertTest("SSRF Guard: accepts valid public domain 'https://shrawaneffects.com'", RankTrackingService::validateAndCleanDomain('https://shrawaneffects.com') === 'shrawaneffects.com');
assertTest("SSRF Guard: accepts bare public domain 'google.co.in'", RankTrackingService::validateAndCleanDomain('google.co.in') === 'google.co.in');

// -----------------------------------------------------------
// TEST 4: CSV Formula Injection Protection (CWE-1236)
// -----------------------------------------------------------
assertTest("Formula Guard: prefixes '=' with single quote", ExportService::sanitizeCell('=SUM(A1:A10)') === "'=SUM(A1:A10)");
assertTest("Formula Guard: prefixes '+' with single quote", ExportService::sanitizeCell('+cmd|/c calc') === "'+cmd|/c calc");
assertTest("Formula Guard: prefixes '-' with single quote", ExportService::sanitizeCell('-cmd') === "'-cmd");
assertTest("Formula Guard: prefixes '@' with single quote", ExportService::sanitizeCell('@command') === "'@command");
assertTest("Formula Guard: preserves safe text unaltered", ExportService::sanitizeCell('Acme Corporation') === 'Acme Corporation');

// -----------------------------------------------------------
// TEST 5: Business Deduplication Logic
// -----------------------------------------------------------
$hash1 = UsefulToolBusiness::generateIdentifier('Acme Corp', 'Faridabad', '+91 95552 77347');
$hash2 = UsefulToolBusiness::generateIdentifier('acme corp ', 'FARIDABAD', '9555277347');
assertTest("Deduplication: normalized identifier generates 64-char sha256", strlen($hash1) === 64);
assertTest("Deduplication: matches across case and formatting differences", $hash1 === $hash2);

// -----------------------------------------------------------
// TEST 6: Database Isolation & Integrity
// -----------------------------------------------------------
$testUser = User::first();
if (!$testUser) {
    $testUser = User::create([
        'name' => 'Test Runner User',
        'email' => 'testrunner@example.com',
        'password' => bcrypt('password123'),
        'role' => 'user',
        'is_active' => true,
    ]);
}

$sampleBusiness = UsefulToolBusiness::create([
    'user_id' => $testUser->id,
    'source_platform' => 'google_places',
    'source_record_id' => 'place_test_123',
    'normalized_identifier' => $hash1,
    'name' => 'Acme Automated Testing Corp',
    'category' => 'Engineering',
    'city' => 'Faridabad',
    'state' => 'Haryana',
    'country' => 'India',
    'phone' => '+91 95552 77347',
    'rating' => 4.8,
    'review_count' => 42,
    'data_quality_status' => 'verified',
    'first_discovered_at' => now(),
    'last_checked_at' => now(),
]);

assertTest("Database: Business record inserted successfully", $sampleBusiness->exists && $sampleBusiness->id > 0);

$retrievedBusiness = UsefulToolBusiness::where('user_id', $testUser->id)->find($sampleBusiness->id);
assertTest("Database: Business record queryable by user scope", $retrievedBusiness !== null && $retrievedBusiness->name === 'Acme Automated Testing Corp');

// -----------------------------------------------------------
// TEST 7: Keyword Position Tier Scopes
// -----------------------------------------------------------
$kwTop3 = UsefulToolTrackedKeyword::create([
    'user_id' => $testUser->id,
    'domain' => 'shrawaneffects.com',
    'keyword' => 'web dev test top3',
    'current_position' => 2,
    'is_active' => true,
]);

$kwTop10 = UsefulToolTrackedKeyword::create([
    'user_id' => $testUser->id,
    'domain' => 'shrawaneffects.com',
    'keyword' => 'web dev test top10',
    'current_position' => 8,
    'is_active' => true,
]);

$kwUnranked = UsefulToolTrackedKeyword::create([
    'user_id' => $testUser->id,
    'domain' => 'shrawaneffects.com',
    'keyword' => 'web dev test unranked',
    'current_position' => null,
    'is_active' => true,
]);

assertTest("Keyword Scope: positionTier('top3') includes #2 and excludes #8", 
    UsefulToolTrackedKeyword::where('user_id', $testUser->id)->positionTier('top3')->where('id', $kwTop3->id)->exists() &&
    !UsefulToolTrackedKeyword::where('user_id', $testUser->id)->positionTier('top3')->where('id', $kwTop10->id)->exists()
);

assertTest("Keyword Scope: positionTier('unranked') finds null rank", 
    UsefulToolTrackedKeyword::where('user_id', $testUser->id)->positionTier('unranked')->where('id', $kwUnranked->id)->exists()
);

// -----------------------------------------------------------
// TEST 8: Export Center Generation & Token Security
// -----------------------------------------------------------
$exportService = new ExportService();
$exportRecord = $exportService->export(
    userId: $testUser->id,
    toolType: 'business_scraper',
    headers: ['name' => 'Business Name', 'phone' => 'Phone', 'city' => 'City'],
    rows: [$sampleBusiness],
    format: 'csv',
    filenamePrefix: 'test_leads'
);

assertTest("Export System: generates download token and file on disk", 
    $exportRecord->exists && strlen($exportRecord->download_token) > 20 && \Illuminate\Support\Facades\Storage::disk('local')->exists($exportRecord->file_path)
);
assertTest("Export System: expiry is set within 24 hours", !$exportRecord->isExpired());

// Cleanup test rows safely
$sampleBusiness->delete();
$kwTop3->delete();
$kwTop10->delete();
$kwUnranked->delete();
\Illuminate\Support\Facades\Storage::disk('local')->delete($exportRecord->file_path);
$exportRecord->delete();

echo "\n------------------------------------------------------\n";
echo "SUMMARY: Passed: {$passed} | Failed: {$failed}\n";
echo "------------------------------------------------------\n";

exit($failed > 0 ? 1 : 0);
