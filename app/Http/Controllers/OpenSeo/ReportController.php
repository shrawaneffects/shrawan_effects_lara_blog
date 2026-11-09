<?php

namespace App\Http\Controllers\OpenSeo;

use App\Http\Controllers\Controller;
use App\Models\OpenSeo\OpenSeoProject;
use App\Models\OpenSeo\OpenSeoReport;
use App\Services\OpenSeo\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) {
            return redirect()->route('openseo.projects.index');
        }

        $reports = $activeProject->reports()->latest()->paginate(10);

        return view('frontend.openseo.reports', compact('activeProject', 'reports'));
    }

    public function generate(Request $request, ReportService $service)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $request->validate([
            'title' => 'required|string|max:150',
            'report_type' => 'nullable|string',
        ]);

        $report = $service->generateReport(
            $activeProject,
            Auth::user(),
            $request->title,
            $request->report_type ?: 'executive_audit'
        );

        return redirect()->route('openseo.reports.show', $report->share_token)
            ->with('success', "Report '{$report->title}' generated successfully.");
    }

    public function show($share_token)
    {
        $report = OpenSeoReport::where('share_token', $share_token)->with('project')->firstOrFail();

        return view('frontend.openseo.report-view', compact('report'));
    }

    public function exportCsv($type)
    {
        $activeProject = OpenSeoProject::getActiveProject();
        if (!$activeProject) return redirect()->back();

        $filename = "openseo-{$activeProject->domain}-{$type}-" . date('Y-m-d') . ".csv";

        return new StreamedResponse(function () use ($activeProject, $type) {
            $handle = fopen('php://output', 'w');

            if ($type === 'keywords') {
                fputcsv($handle, ['Keyword', 'Search Volume', 'CPC ($)', 'KD (%)', 'Intent', 'Current Rank', 'Best Rank', 'Rank Change', 'Target URL']);
                foreach ($activeProject->keywords as $kw) {
                    fputcsv($handle, [
                        $kw->keyword,
                        $kw->search_volume,
                        $kw->cpc,
                        $kw->keyword_difficulty,
                        $kw->search_intent,
                        $kw->current_rank ?: 'N/A',
                        $kw->best_rank ?: 'N/A',
                        $kw->rank_change,
                        $kw->target_url ?: '',
                    ]);
                }
            } elseif ($type === 'backlinks') {
                fputcsv($handle, ['Source URL', 'Source Title', 'Target URL', 'Anchor Text', 'Domain Rating', 'Type', 'First Seen']);
                foreach ($activeProject->backlinks as $bl) {
                    fputcsv($handle, [
                        $bl->source_url,
                        $bl->source_title,
                        $bl->target_url,
                        $bl->anchor_text,
                        $bl->domain_rating,
                        $bl->is_dofollow ? 'dofollow' : 'nofollow',
                        $bl->first_seen,
                    ]);
                }
            } elseif ($type === 'audit') {
                fputcsv($handle, ['Page URL', 'Issue Type', 'Severity', 'HTTP Status', 'Description', 'Recommendation']);
                $latestAudit = $activeProject->audits()->latest()->first();
                if ($latestAudit) {
                    foreach ($latestAudit->issues as $issue) {
                        fputcsv($handle, [
                            $issue->page_url,
                            $issue->issue_type,
                            $issue->severity,
                            $issue->http_status,
                            $issue->description,
                            $issue->recommendation,
                        ]);
                    }
                }
            }

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
