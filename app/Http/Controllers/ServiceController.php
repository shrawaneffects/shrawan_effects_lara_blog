<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display all services offered by Shrawan Effects.
     */
    public function index()
    {
        $services = Service::active()->ordered()->get();
        $featuredServices = Service::active()->featured()->ordered()->get();

        $siteName = Setting::get('site_name', config('app.name', 'Shrawan Effects'));

        return view('frontend.services.index', compact('services', 'featuredServices', 'siteName'));
    }

    /**
     * Display individual service detail page.
     */
    public function show(string $slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();
        $relatedServices = Service::active()->where('id', '!=', $service->id)->take(3)->get();
        $siteName = Setting::get('site_name', config('app.name', 'Shrawan Effects'));

        return view('frontend.services.show', compact('service', 'relatedServices', 'siteName'));
    }
}
