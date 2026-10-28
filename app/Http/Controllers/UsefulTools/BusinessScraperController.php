<?php

namespace App\Http\Controllers\UsefulTools;

use App\Http\Controllers\Controller;
use App\Jobs\UsefulTools\ProcessBusinessScrapingJob;
use App\Models\UsefulTools\UsefulToolBusiness;
use App\Models\UsefulTools\UsefulToolScrapingJob;
use App\Services\UsefulTools\BusinessData\BusinessDataScraperService;
use App\Services\UsefulTools\Exports\ExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BusinessScraperController extends Controller
{
    public function __construct(
        protected BusinessDataScraperService $scraperService,
        protected ExportService $exportService
    ) {
    }

    /**
     * Display the Business Data Scraper dashboard, search form, and collected records.
     */
    public function index(Request $request)
    {
        $userId = auth()->id();
        $providers = $this->scraperService->getAvailableProviders();

        // Query businesses with search & filtering
        $query = UsefulToolBusiness::where('user_id', $userId);

        if ($request->filled('search')) {
            $s = trim($request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('category', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('website', 'like', "%{$s}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('provider')) {
            $query->where('source_platform', $request->provider);
        }

        if ($request->filled('has_phone')) {
            $query->whereNotNull('phone')->where('phone', '!=', '');
        }

        if ($request->filled('has_website')) {
            $query->whereNotNull('website')->where('website', '!=', '');
        }

        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');
        if (in_array($sort, ['name', 'rating', 'review_count', 'created_at', 'city'])) {
            $query->orderBy($sort, $direction === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $businesses = $query->paginate(15)->withQueryString();

        // Recent collection jobs
        $recentJobs = UsefulToolScrapingJob::where('user_id', $userId)
            ->latest()
            ->take(8)
            ->get();

        // City distinct list for filter
        $cities = UsefulToolBusiness::where('user_id', $userId)
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city');

        // Metrics
        $totalBusinesses = UsefulToolBusiness::where('user_id', $userId)->count();
        $verifiedContacts = UsefulToolBusiness::where('user_id', $userId)->whereNotNull('phone')->count();
        $totalJobs = UsefulToolScrapingJob::where('user_id', $userId)->count();

        return view('frontend.useful-tools.business-scraper.index', compact(
            'providers',
            'businesses',
            'recentJobs',
            'cities',
            'totalBusinesses',
            'verifiedContacts',
            'totalJobs'
        ));
    }

    /**
     * Submit and initiate a business scraping job.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'keyword' => 'required|string|max:150',
            'category' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'provider' => 'required|string|in:google_places,google_search,yelp,indiamart,justdial',
            'max_results' => 'nullable|integer|min:5|max:60',
            'run_mode' => 'nullable|string|in:async,sync',
        ]);

        $provider = $this->scraperService->getProvider($validated['provider']);
        if (!$provider || !$provider->isConfigured()) {
            return back()->withInput()->with('error', "Selected provider '{$validated['provider']}' is not yet configured. Please supply API credentials or select another provider.");
        }

        $job = UsefulToolScrapingJob::create([
            'user_id' => auth()->id(),
            'provider' => $validated['provider'],
            'query_keyword' => $validated['keyword'],
            'category' => $validated['category'] ?? null,
            'city' => $validated['city'] ?? null,
            'state' => $validated['state'] ?? null,
            'country' => $validated['country'] ?? 'India',
            'max_results' => $validated['max_results'] ?? 20,
            'status' => 'pending',
        ]);

        // Default: If queue driver is sync or user selected sync, execute immediately
        if (config('queue.default') === 'sync' || ($request->run_mode ?? 'sync') === 'sync') {
            $result = $this->scraperService->executeJob($job);
            if ($result['success']) {
                return redirect()->route('useful-tools.business-scraper.index')
                    ->with('success', "Discovered {$result['total_found']} listings. Successfully collected {$result['total_collected']} new verified business records.");
            } else {
                return redirect()->route('useful-tools.business-scraper.index')
                    ->with('warning', "Job finished with notice: " . ($result['error'] ?? 'No records returned.'));
            }
        }

        // Otherwise dispatch to background queue
        ProcessBusinessScrapingJob::dispatch($job);

        return redirect()->route('useful-tools.business-scraper.job-status', $job->id)
            ->with('info', "Business scraping job #{$job->id} dispatched to background queue.");
    }

    /**
     * Real-time job status inquiry (AJAX / polling / view).
     */
    public function showJob(int $id)
    {
        $job = UsefulToolScrapingJob::where('user_id', auth()->id())->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json([
                'id' => $job->id,
                'status' => $job->status,
                'progress_percent' => $job->progress_percent,
                'total_found' => $job->total_found,
                'total_collected' => $job->total_collected,
                'error_message' => $job->error_message,
            ]);
        }

        return view('frontend.useful-tools.business-scraper.job-status', compact('job'));
    }

    /**
     * Cancel an active or pending job.
     */
    public function cancelJob(int $id)
    {
        $job = UsefulToolScrapingJob::where('user_id', auth()->id())->findOrFail($id);
        if ($job->isRunning()) {
            $job->update([
                'status' => 'cancelled',
                'completed_at' => now(),
            ]);
            return back()->with('info', "Job #{$id} has been cancelled.");
        }
        return back()->with('warning', "Job #{$id} is not in a cancellable state.");
    }

    /**
     * Delete a single business record.
     */
    public function destroy(int $id)
    {
        $business = UsefulToolBusiness::where('user_id', auth()->id())->findOrFail($id);
        $business->delete();
        return back()->with('success', 'Business record removed.');
    }

    /**
     * Export businesses to CSV or JSON with formula protection.
     */
    public function export(Request $request)
    {
        $userId = auth()->id();
        $format = $request->get('format', 'csv') === 'json' ? 'json' : 'csv';

        $query = UsefulToolBusiness::where('user_id', $userId);

        // Selected IDs
        if ($request->filled('selected_ids') && is_array($request->selected_ids)) {
            $query->whereIn('id', $request->selected_ids);
        } else {
            // Apply current filters
            if ($request->filled('city')) {
                $query->where('city', $request->city);
            }
            if ($request->filled('provider')) {
                $query->where('source_platform', $request->provider);
            }
        }

        $businesses = $query->orderBy('name')->get();

        if ($businesses->isEmpty()) {
            return back()->with('warning', 'No business records available to export.');
        }

        $headers = [
            'name' => 'Business Name',
            'category' => 'Category',
            'phone' => 'Phone',
            'email' => 'Email',
            'website' => 'Website',
            'address' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'rating' => 'Rating',
            'review_count' => 'Reviews',
            'source_platform' => 'Source Platform',
            'listing_url' => 'Listing URL',
            'first_discovered_at' => 'Discovered Date',
        ];

        $exportRecord = $this->exportService->export(
            userId: $userId,
            toolType: 'business_scraper',
            headers: $headers,
            rows: $businesses,
            format: $format,
            filenamePrefix: 'business_leads'
        );

        return redirect()->route('useful-tools.exports.download', $exportRecord->download_token);
    }
}
