<?php

namespace App\Http\Controllers\UsefulTools;

use App\Http\Controllers\Controller;
use App\Models\UsefulTools\UsefulToolExport;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    /**
     * Display User Export Center with generated CSV and JSON archives.
     */
    public function index()
    {
        $exports = UsefulToolExport::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('frontend.useful-tools.exports.index', compact('exports'));
    }

    /**
     * Download an exported file securely via single-use token or valid active token.
     */
    public function download(string $token)
    {
        $export = UsefulToolExport::where('download_token', $token)->firstOrFail();

        // Enforce user authorization if logged in
        if (auth()->check() && $export->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this export file.');
        }

        if ($export->isExpired()) {
            abort(410, 'This export download link has expired. Please generate a new export.');
        }

        if (!Storage::disk('local')->exists($export->file_path)) {
            abort(404, 'Export file not found on server disk.');
        }

        $fullPath = Storage::disk('local')->path($export->file_path);

        $contentType = $export->format === 'json' ? 'application/json' : 'text/csv; charset=UTF-8';

        return response()->download($fullPath, $export->filename, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
        ]);
    }

    /**
     * Remove an export file from disk and database.
     */
    public function destroy(int $id)
    {
        $export = UsefulToolExport::where('user_id', auth()->id())->findOrFail($id);
        if (Storage::disk('local')->exists($export->file_path)) {
            Storage::disk('local')->delete($export->file_path);
        }
        $export->delete();

        return back()->with('success', 'Export file deleted.');
    }
}
