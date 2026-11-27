<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConvertCaseController extends Controller
{
    /**
     * Display the Convert Case online utility page.
     */
    public function index(): View
    {
        $tool = null;
        try {
            $tool = Tool::where('slug', 'convertcase')->first();
        } catch (\Throwable $e) {}

        return view('frontend.services.convertcase', [
            'tool' => $tool,
            'pageTitle' => $tool?->meta_title ?: 'Convert Case - Online Text Case Converter | Shrawan Effects',
            'metaDescription' => $tool?->meta_description ?: 'Free online Convert Case tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'canonicalUrl' => $tool?->canonical_url ?: route('services.convertcase'),
        ]);
    }
}
