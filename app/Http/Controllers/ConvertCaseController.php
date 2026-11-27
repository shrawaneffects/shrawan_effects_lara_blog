<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ConvertCaseController extends Controller
{
    /**
     * Display the Convert Case online utility page.
     */
    public function index(): View
    {
        return view('frontend.services.convertcase', [
            'pageTitle' => 'Convert Case - Online Text Case Converter | Shrawan Effects',
            'metaDescription' => 'Free online Convert Case tool. Instantly convert text between Sentence case, lower case, UPPER CASE, Capitalized Case, alternating case, Title Case, and InVeRsE CaSe with live character and word counters.',
            'canonicalUrl' => route('services.convertcase'),
        ]);
    }
}
