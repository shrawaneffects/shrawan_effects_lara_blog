<?php

namespace App\Services\Seo;

use App\Models\Post;

class SeoGraderService
{
    /**
     * Compute comprehensive On-Page SEO Grade & deep link analysis
     */
    public static function grade(array $data, ?int $postId = null): array
    {
        $content = $data['content'] ?? '';
        $wordCount = str_word_count(strip_tags($content));

        // 1. Run Standard Modular Audit
        $audit = SeoAuditService::audit($data, $postId);

        // 2. Run Deep Link & Follow/Nofollow Grader
        $linkGrader = SeoLinkGraderService::grade($content, $wordCount, $postId);

        // 3. Compute Category Grades (A+ to F)
        $categories = [
            'technical' => self::computeCategoryGrade($audit['breakdown']['technical']['score'] ?? 0, $audit['breakdown']['technical']['max_score'] ?? 25),
            'content' => self::computeCategoryGrade($audit['breakdown']['content']['score'] ?? 0, $audit['breakdown']['content']['max_score'] ?? 25),
            'on_page' => self::computeCategoryGrade($audit['breakdown']['on_page']['score'] ?? 0, $audit['breakdown']['on_page']['max_score'] ?? 20),
            'internal_linking' => self::computeCategoryGrade($linkGrader['link_score'] ?? 0, 100),
            'image' => self::computeCategoryGrade($audit['breakdown']['image']['score'] ?? 0, $audit['breakdown']['image']['max_score'] ?? 10),
            'structured_data' => self::computeCategoryGrade($audit['breakdown']['structured_data']['score'] ?? 0, $audit['breakdown']['structured_data']['max_score'] ?? 5),
            'social' => self::computeCategoryGrade($audit['breakdown']['social']['score'] ?? 0, $audit['breakdown']['social']['max_score'] ?? 5),
        ];

        // 4. Overall Weighted Score & Letter Grade
        $totalScore = $audit['total_score'];
        $letterGrade = self::getLetterGrade($totalScore);

        return [
            'total_score' => $totalScore,
            'letter_grade' => $letterGrade['grade'],
            'grade_color' => $letterGrade['color'],
            'grade_label' => $letterGrade['label'],
            'categories' => $categories,
            'link_grader' => $linkGrader,
            'audit' => $audit,
            'summary' => [
                'word_count' => $wordCount,
                'total_links' => $linkGrader['total_links'],
                'internal_dofollow' => $linkGrader['internal']['dofollow'],
                'internal_nofollow' => $linkGrader['internal']['nofollow'],
                'external_dofollow' => $linkGrader['external']['dofollow'],
                'external_nofollow' => $linkGrader['external']['nofollow'],
                'external_sponsored' => $linkGrader['external']['sponsored'],
                'external_ugc' => $linkGrader['external']['ugc'],
                'critical_issues' => $audit['summary']['critical'] ?? 0,
                'warning_issues' => $audit['summary']['warnings'] ?? 0,
            ]
        ];
    }

    public static function getLetterGrade(int $score): array
    {
        if ($score >= 95) {
            return ['grade' => 'A+', 'color' => 'success', 'label' => 'Exceptional (A+)'];
        } elseif ($score >= 90) {
            return ['grade' => 'A', 'color' => 'success', 'label' => 'Great (A)'];
        } elseif ($score >= 85) {
            return ['grade' => 'A-', 'color' => 'info', 'label' => 'Very Good (A-)'];
        } elseif ($score >= 80) {
            return ['grade' => 'B+', 'color' => 'info', 'label' => 'Good (B+)'];
        } elseif ($score >= 75) {
            return ['grade' => 'B', 'color' => 'primary', 'label' => 'Above Average (B)'];
        } elseif ($score >= 70) {
            return ['grade' => 'B-', 'color' => 'warning', 'label' => 'Decent (B-)'];
        } elseif ($score >= 65) {
            return ['grade' => 'C+', 'color' => 'warning', 'label' => 'Needs Improvement (C+)'];
        } elseif ($score >= 60) {
            return ['grade' => 'C', 'color' => 'warning', 'label' => 'Fair (C)'];
        } elseif ($score >= 50) {
            return ['grade' => 'D', 'color' => 'danger', 'label' => 'Poor (D)'];
        } else {
            return ['grade' => 'F', 'color' => 'danger', 'label' => 'Critical Failure (F)'];
        }
    }

    protected static function computeCategoryGrade(int $score, int $max): array
    {
        $pct = $max > 0 ? round(($score / $max) * 100) : 0;
        $lg = self::getLetterGrade($pct);

        return [
            'score' => $score,
            'max' => $max,
            'percentage' => $pct,
            'grade' => $lg['grade'],
            'color' => $lg['color'],
        ];
    }
}
