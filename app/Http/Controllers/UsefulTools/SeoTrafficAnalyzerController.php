<?php

namespace App\Http\Controllers\UsefulTools;

use App\Http\Controllers\Controller;
use App\Models\UsefulTools\UsefulToolCompetitor;
use App\Models\UsefulTools\UsefulToolRanking;
use App\Models\UsefulTools\UsefulToolTrackedKeyword;
use App\Models\UsefulTools\UsefulToolTrafficSnapshot;
use App\Services\UsefulTools\Exports\ExportService;
use App\Services\UsefulTools\Seo\KeywordDiscoveryService;
use App\Services\UsefulTools\Seo\RankTrackingService;
use App\Services\UsefulTools\Seo\TrafficAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SeoTrafficAnalyzerController extends Controller
{
    public function __construct(
        protected RankTrackingService $rankService,
        protected KeywordDiscoveryService $keywordService,
        protected TrafficAnalysisService $trafficService,
        protected ExportService $exportService
    ) {
    }

    /**
     * Dashboard Overview with KPI metrics, tracked domains, and ranking distributions.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Distinct domains tracked by user
        $trackedDomains = UsefulToolTrackedKeyword::where('user_id', $userId)
            ->select('domain', DB::raw('count(*) as keywords_count'))
            ->groupBy('domain')
            ->get();

        // Global KPIs
        $totalTrackedKeywords = UsefulToolTrackedKeyword::where('user_id', $userId)->count();
        $top3Count = UsefulToolTrackedKeyword::where('user_id', $userId)->whereBetween('current_position', [1, 3])->count();
        $top10Count = UsefulToolTrackedKeyword::where('user_id', $userId)->whereBetween('current_position', [1, 10])->count();
        $top100Count = UsefulToolTrackedKeyword::where('user_id', $userId)->whereBetween('current_position', [1, 100])->count();
        $improvedCount = UsefulToolTrackedKeyword::where('user_id', $userId)->where('position_change', '>', 0)->count();
        $declinedCount = UsefulToolTrackedKeyword::where('user_id', $userId)->where('position_change', '<', 0)->count();

        // Total Estimated Traffic Across Domains
        $totalEstimatedVisits = UsefulToolTrafficSnapshot::where('user_id', $userId)
            ->where('data_type', 'estimated_third_party')
            ->latest('snapshot_date')
            ->sum('monthly_visits');

        // Providers status
        $providersStatus = $this->trafficService->getProvidersStatus();

        // Recent ranking activity
        $recentRankings = UsefulToolRanking::whereHas('trackedKeyword', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with('trackedKeyword')->latest('checked_date')->take(10)->get();

        return view('frontend.useful-tools.seo-traffic.index', compact(
            'trackedDomains',
            'totalTrackedKeywords',
            'top3Count',
            'top10Count',
            'top100Count',
            'improvedCount',
            'declinedCount',
            'totalEstimatedVisits',
            'providersStatus',
            'recentRankings'
        ));
    }

    /**
     * Domain detail view: keyword discovery, 1-100 positions, trends, and competitors.
     */
    public function domainDetail(Request $request, string $domain)
    {
        $userId = auth()->id();
        $cleanDomain = RankTrackingService::validateAndCleanDomain($domain);

        if (!$cleanDomain) {
            return redirect()->route('useful-tools.seo-traffic.index')
                ->with('error', 'Invalid domain or restricted private network address.');
        }

        // Keywords query with filtering
        $query = UsefulToolTrackedKeyword::where('user_id', $userId)
            ->where('domain', $cleanDomain);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where('keyword', 'like', "%{$s}%");
        }

        if ($request->filled('tier')) {
            $query->positionTier($request->tier);
        }

        $keywords = $query->orderBy('current_position', 'asc')->paginate(20)->withQueryString();

        // Domain Specific KPIs
        $domainTotalKeywords = UsefulToolTrackedKeyword::where('user_id', $userId)->where('domain', $cleanDomain)->count();
        $domainTop3 = UsefulToolTrackedKeyword::where('user_id', $userId)->where('domain', $cleanDomain)->whereBetween('current_position', [1, 3])->count();
        $domainTop10 = UsefulToolTrackedKeyword::where('user_id', $userId)->where('domain', $cleanDomain)->whereBetween('current_position', [1, 10])->count();
        $domainTop100 = UsefulToolTrackedKeyword::where('user_id', $userId)->where('domain', $cleanDomain)->whereBetween('current_position', [1, 100])->count();
        $domainUnranked = UsefulToolTrackedKeyword::where('user_id', $userId)->where('domain', $cleanDomain)->whereNull('current_position')->count();

        // Traffic Snapshots
        $trafficSnapshots = UsefulToolTrafficSnapshot::where('user_id', $userId)
            ->where('domain', $cleanDomain)
            ->latest('snapshot_date')
            ->take(6)
            ->get();

        $latestTraffic = $trafficSnapshots->first();

        // Competitor comparisons
        $competitorAnalysis = $this->trafficService->compareDomainWithCompetitors($userId, $cleanDomain);

        // Ranking history chart data for top 5 keywords
        $chartKeywords = UsefulToolTrackedKeyword::where('user_id', $userId)
            ->where('domain', $cleanDomain)
            ->whereNotNull('current_position')
            ->orderBy('current_position')
            ->take(5)
            ->with(['rankings' => function ($q) {
                $q->orderBy('checked_date', 'asc')->take(14);
            }])
            ->get();

        return view('frontend.useful-tools.seo-traffic.domain-detail', compact(
            'cleanDomain',
            'keywords',
            'domainTotalKeywords',
            'domainTop3',
            'domainTop10',
            'domainTop100',
            'domainUnranked',
            'trafficSnapshots',
            'latestTraffic',
            'competitorAnalysis',
            'chartKeywords'
        ));
    }

    /**
     * Add a target keyword to track for a domain.
     */
    public function addKeyword(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:200',
            'keyword' => 'required|string|max:200',
            'country' => 'nullable|string|max:10',
            'device' => 'nullable|string|in:desktop,mobile',
        ]);

        $cleanDomain = RankTrackingService::validateAndCleanDomain($validated['domain']);
        if (!$cleanDomain) {
            return back()->with('error', 'Invalid domain entered or restricted host address.');
        }

        $keyword = UsefulToolTrackedKeyword::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'domain' => $cleanDomain,
                'keyword' => trim($validated['keyword']),
            ],
            [
                'country' => $validated['country'] ?? 'IN',
                'device' => $validated['device'] ?? 'desktop',
                'search_engine' => 'google',
            ]
        );

        return redirect()->route('useful-tools.seo-traffic.domain-detail', $cleanDomain)
            ->with('success', "Keyword '{$keyword->keyword}' added to tracking list.");
    }

    /**
     * Check live SERP position 1–100 for a tracked keyword.
     */
    public function checkRank(int $id)
    {
        $keyword = UsefulToolTrackedKeyword::where('user_id', auth()->id())->findOrFail($id);
        $result = $this->rankService->checkKeywordRank($keyword);

        if (!$result['success']) {
            return back()->with('warning', "SERP check notice: " . ($result['error'] ?? 'Unable to complete rank check.'));
        }

        $posLabel = $result['is_found'] ? "Position #{$result['position']}" : "Not ranked in checked Top 100";
        return back()->with('success', "Checked '{$keyword->keyword}': {$posLabel}.");
    }

    /**
     * Discover keywords for a domain via authorized SEO provider.
     */
    public function discoverKeywords(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:200',
            'country' => 'nullable|string|max:10',
        ]);

        $cleanDomain = RankTrackingService::validateAndCleanDomain($validated['domain']);
        if (!$cleanDomain) {
            return back()->with('error', 'Invalid domain or restricted host address.');
        }

        $result = $this->keywordService->discoverAndSaveKeywords(
            userId: auth()->id(),
            domain: $cleanDomain,
            country: $validated['country'] ?? 'in'
        );

        if (!$result['success']) {
            return back()->with('warning', "Keyword discovery notice: " . ($result['error'] ?? 'Could not retrieve keywords.'));
        }

        return redirect()->route('useful-tools.seo-traffic.domain-detail', $cleanDomain)
            ->with('success', "Discovered {$result['count']} new organic ranking keywords for {$cleanDomain}.");
    }

    /**
     * Add competitor domain for overlap comparison.
     */
    public function addCompetitor(Request $request)
    {
        $validated = $request->validate([
            'main_domain' => 'required|string|max:200',
            'competitor_domain' => 'required|string|max:200',
            'notes' => 'nullable|string|max:255',
        ]);

        $cleanMain = RankTrackingService::validateAndCleanDomain($validated['main_domain']);
        $cleanComp = RankTrackingService::validateAndCleanDomain($validated['competitor_domain']);

        if (!$cleanMain || !$cleanComp) {
            return back()->with('error', 'Invalid domain or competitor host address.');
        }

        if ($cleanMain === $cleanComp) {
            return back()->with('warning', 'Competitor domain cannot be identical to the main domain.');
        }

        UsefulToolCompetitor::firstOrCreate(
            [
                'user_id' => auth()->id(),
                'main_domain' => $cleanMain,
                'competitor_domain' => $cleanComp,
            ],
            ['notes' => $validated['notes'] ?? null]
        );

        return back()->with('success', "Competitor '{$cleanComp}' linked for keyword and traffic comparison.");
    }

    /**
     * Fetch domain traffic snapshot from provider.
     */
    public function fetchTraffic(Request $request)
    {
        $validated = $request->validate([
            'domain' => 'required|string|max:200',
            'country' => 'nullable|string|max:10',
        ]);

        $cleanDomain = RankTrackingService::validateAndCleanDomain($validated['domain']);
        if (!$cleanDomain) {
            return back()->with('error', 'Invalid domain entered.');
        }

        $result = $this->trafficService->recordThirdPartyTrafficSnapshot(
            userId: auth()->id(),
            domain: $cleanDomain,
            country: $validated['country'] ?? 'in'
        );

        if (!$result['success']) {
            return back()->with('warning', "Traffic analytics notice: " . ($result['error'] ?? 'Unable to retrieve traffic estimates.'));
        }

        return back()->with('success', "Updated traffic metrics snapshot for {$cleanDomain}.");
    }

    /**
     * Export domain keyword rankings with formula protection.
     */
    public function exportKeywords(Request $request, string $domain)
    {
        $userId = auth()->id();
        $cleanDomain = RankTrackingService::validateAndCleanDomain($domain);

        if (!$cleanDomain) {
            return back()->with('error', 'Invalid domain.');
        }

        $keywords = UsefulToolTrackedKeyword::where('user_id', $userId)
            ->where('domain', $cleanDomain)
            ->orderBy('current_position', 'asc')
            ->get();

        if ($keywords->isEmpty()) {
            return back()->with('warning', 'No keywords to export for this domain.');
        }

        $headers = [
            'domain' => 'Domain',
            'keyword' => 'Search Keyword',
            'current_position' => 'Rank Position (1–100)',
            'previous_position' => 'Previous Position',
            'position_change' => 'Position Change',
            'best_position' => 'Best Rank',
            'ranking_url' => 'Ranking URL',
            'search_volume' => 'Search Volume (Est.)',
            'keyword_difficulty' => 'Difficulty (0–100)',
            'cpc' => 'CPC (USD)',
            'search_intent' => 'Search Intent',
            'last_checked_at' => 'Last Checked',
        ];

        $exportRecord = $this->exportService->export(
            userId: $userId,
            toolType: 'seo_keywords',
            headers: $headers,
            rows: $keywords,
            format: 'csv',
            filenamePrefix: "seo_rankings_{$cleanDomain}"
        );

        return redirect()->route('useful-tools.exports.download', $exportRecord->download_token);
    }
}
