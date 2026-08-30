<?php

namespace App\Services\Seo;

class SeoScoreService
{
    /**
     * Category maximum weightings (Total: 100)
     */
    public const WEIGHTS = [
        'technical' => 25,
        'content' => 25,
        'on_page' => 20,
        'internal_linking' => 10,
        'image' => 10,
        'structured_data' => 5,
        'social' => 5,
    ];

    /**
     * Compute total SEO score and category breakdown from audit check results
     *
     * @param array $auditResults Array of rule results: [ 'technical' => [ ['passed' => true, 'deduction' => 0, ...], ... ], ... ]
     * @return array
     */
    public static function calculate(array $auditResults): array
    {
        $breakdown = [];
        $totalScore = 0;
        $allRules = [];
        $criticalCount = 0;
        $warningCount = 0;
        $passedCount = 0;

        foreach (self::WEIGHTS as $category => $maxScore) {
            $categoryRules = $auditResults[$category] ?? [];
            $categoryDeductions = 0;

            foreach ($categoryRules as $rule) {
                $allRules[] = $rule;
                if (($rule['severity'] ?? '') === 'critical') {
                    $criticalCount++;
                } elseif (($rule['severity'] ?? '') === 'warning') {
                    $warningCount++;
                } elseif (($rule['passed'] ?? false)) {
                    $passedCount++;
                }

                $deduction = (int) ($rule['deduction'] ?? 0);
                $categoryDeductions += $deduction;
            }

            $catScore = max(0, $maxScore - $categoryDeductions);
            $breakdown[$category] = [
                'score' => $catScore,
                'max_score' => $maxScore,
                'percentage' => $maxScore > 0 ? round(($catScore / $maxScore) * 100) : 100,
                'rules' => $categoryRules,
            ];

            $totalScore += $catScore;
        }

        $totalScore = max(0, min(100, $totalScore));

        // Determine status
        $status = 'good';
        $statusLabel = 'Good / Optimized';
        $statusColor = 'success';

        if ($criticalCount > 0 || $totalScore < 50) {
            $status = 'critical';
            $statusLabel = 'Critical Issues Found';
            $statusColor = 'danger';
        } elseif ($warningCount > 2 || $totalScore < 75) {
            $status = 'needs_review';
            $statusLabel = 'Recommendations Available';
            $statusColor = 'warning';
        }

        return [
            'total_score' => $totalScore,
            'status' => $status,
            'status_label' => $statusLabel,
            'status_color' => $statusColor,
            'breakdown' => $breakdown,
            'summary' => [
                'passed' => $passedCount,
                'warnings' => $warningCount,
                'critical' => $criticalCount,
                'total_rules' => count($allRules),
            ],
            'rules' => $allRules,
        ];
    }
}
