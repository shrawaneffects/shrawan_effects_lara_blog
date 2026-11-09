<?php

namespace App\Services\OpenSeo;

use App\Models\OpenSeo\OpenSeoProject;
use App\Models\OpenSeo\OpenSeoReport;
use App\Models\User;
use Illuminate\Support\Str;

class ReportService
{
    /**
     * Generate an executive audit report for a project.
     */
    public function generateReport(OpenSeoProject $project, ?User $user, string $title, string $type = 'executive_audit'): OpenSeoReport
    {
        $domainService = app(DomainOverviewService::class);
        $overview = $domainService->getOverview($project);

        $auditService = app(SiteAuditService::class);
        $audit = $auditService->getLatestAudit($project);

        $summaryData = [
            'project_name' => $project->name,
            'domain' => $project->domain,
            'target_country' => $project->target_country,
            'authority_score' => $overview['authority_score'],
            'organic_traffic' => $overview['organic_traffic'],
            'total_keywords' => $project->keywords()->count(),
            'tracked_keywords' => $project->keywords()->where('is_tracked', true)->count(),
            'total_backlinks' => $overview['backlinks_summary']['total_backlinks'] ?? 0,
            'referring_domains' => $overview['backlinks_summary']['referring_domains'] ?? 0,
            'health_score' => $audit?->health_score ?? 90,
            'total_audit_issues' => $audit?->issues()->count() ?? 0,
            'generated_at' => now()->toDateTimeString(),
        ];

        return OpenSeoReport::create([
            'project_id' => $project->id,
            'user_id' => $user?->id,
            'title' => $title,
            'report_type' => $type,
            'summary_data' => $summaryData,
            'share_token' => Str::random(32),
            'is_public' => true,
        ]);
    }
}
