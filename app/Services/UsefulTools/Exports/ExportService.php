<?php

namespace App\Services\UsefulTools\Exports;

use App\Models\UsefulTools\UsefulToolExport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExportService
{
    protected string $storageDisk = 'local';
    protected string $exportDirectory = 'useful_tool_exports';

    /**
     * Sanitize cell value to prevent CSV Formula Injection (CWE-1236).
     */
    public static function sanitizeCell(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        $str = (string) $value;
        if (strlen($str) > 0) {
            $firstChar = $str[0];
            // Disallow formula triggers: =, +, -, @, Tab, Carriage Return
            if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"])) {
                return "'" . $str;
            }
        }

        return $str;
    }

    /**
     * Export dataset to CSV or JSON with expiration token.
     *
     * @param int $userId
     * @param string $toolType ('business_scraper', 'seo_keywords', 'seo_rankings', 'seo_traffic')
     * @param array $headers Column header array
     * @param iterable $rows Rows array or cursor
     * @param string $format ('csv', 'json')
     * @param string $filenamePrefix
     * @return UsefulToolExport
     */
    public function export(
        int $userId,
        string $toolType,
        array $headers,
        iterable $rows,
        string $format = 'csv',
        string $filenamePrefix = 'export'
    ): UsefulToolExport {
        if (!Storage::disk($this->storageDisk)->exists($this->exportDirectory)) {
            Storage::disk($this->storageDisk)->makeDirectory($this->exportDirectory);
        }

        $timestamp = now()->format('Ymd_His');
        $random = Str::random(8);
        $filename = "{$filenamePrefix}_{$timestamp}_{$random}.{$format}";
        $relativePath = "{$this->exportDirectory}/{$filename}";
        $fullPath = Storage::disk($this->storageDisk)->path($relativePath);

        $totalRecords = 0;

        if ($format === 'json') {
            $jsonRows = [];
            foreach ($rows as $row) {
                $totalRecords++;
                $jsonRows[] = (array) $row;
            }
            Storage::disk($this->storageDisk)->put($relativePath, json_encode($jsonRows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            // Default: Secure streaming CSV
            $fileHandle = fopen($fullPath, 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($fileHandle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Write headers
            fputcsv($fileHandle, array_map([self::class, 'sanitizeCell'], $headers));

            foreach ($rows as $row) {
                $totalRecords++;
                $rowArray = (array) $row;
                $sanitizedRow = [];
                foreach ($headers as $key => $headerName) {
                    $val = $rowArray[$key] ?? ($rowArray[$headerName] ?? '');
                    $sanitizedRow[] = self::sanitizeCell($val);
                }
                fputcsv($fileHandle, $sanitizedRow);
            }

            fclose($fileHandle);
        }

        $fileSizeBytes = Storage::disk($this->storageDisk)->size($relativePath);

        return UsefulToolExport::create([
            'user_id' => $userId,
            'tool_type' => $toolType,
            'filename' => $filename,
            'file_path' => $relativePath,
            'format' => $format,
            'total_records' => $totalRecords,
            'file_size_bytes' => $fileSizeBytes,
            'download_token' => UsefulToolExport::generateToken(),
            'expires_at' => now()->addHours(24),
        ]);
    }
}
