<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BackupService
{
    protected static string $backupDir = 'backups';

    /**
     * Create a full database snapshot archive.
     */
    public static function createBackup(): array
    {
        if (!Storage::disk('local')->exists(self::$backupDir)) {
            Storage::disk('local')->makeDirectory(self::$backupDir);
        }

        $tables = Schema::getTableListing();
        $dbName = config('database.connections.mysql.database', 'laravel_blog');
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "backup_{$dbName}_{$timestamp}.json";
        $filePath = self::$backupDir . '/' . $filename;

        $snapshot = [
            'meta' => [
                'database' => $dbName,
                'created_at' => now()->toIso8601String(),
                'version' => '1.0',
                'app_name' => config('app.name', 'Laravel Blog'),
                'tables_count' => count($tables),
            ],
            'data' => [],
        ];

        foreach ($tables as $table) {
            // Skip migrations or jobs table if empty or not needed, or include all
            $rows = DB::table($table)->get()->toArray();
            // Convert stdClass to array
            $snapshot['data'][$table] = array_map(function ($item) {
                return (array) $item;
            }, $rows);
        }

        $jsonContent = json_encode($snapshot, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        Storage::disk('local')->put($filePath, $jsonContent);

        $fileSizeBytes = Storage::disk('local')->size($filePath);
        $md5 = md5($jsonContent);

        // Record in Audit Log
        AuditLog::record(
            'backup_created',
            "Generated full database snapshot '{$filename}' (" . self::formatBytes($fileSizeBytes) . ")",
            auth()->id(),
            [
                'filename' => $filename,
                'tables_count' => count($tables),
                'size_bytes' => $fileSizeBytes,
                'checksum_md5' => $md5,
            ],
            'info'
        );

        return [
            'filename' => $filename,
            'size' => self::formatBytes($fileSizeBytes),
            'tables' => count($tables),
            'checksum' => $md5,
            'path' => $filePath,
        ];
    }

    /**
     * Get list of all available backups.
     */
    public static function getBackupsList(): array
    {
        if (!Storage::disk('local')->exists(self::$backupDir)) {
            return [];
        }

        $files = Storage::disk('local')->files(self::$backupDir);
        $backups = [];

        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'json') {
                $filename = basename($file);
                $sizeBytes = Storage::disk('local')->size($file);
                $lastModified = Storage::disk('local')->lastModified($file);

                $backups[] = [
                    'filename' => $filename,
                    'size' => self::formatBytes($sizeBytes),
                    'size_bytes' => $sizeBytes,
                    'created_at' => date('M d, Y H:i:s', $lastModified),
                    'timestamp' => $lastModified,
                ];
            }
        }

        // Sort latest backups first
        usort($backups, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return $backups;
    }

    /**
     * Restore database from a selected backup snapshot.
     */
    public static function restoreBackup(string $filename): bool
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        $filePath = self::$backupDir . '/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            throw new \Exception("Backup file '{$filename}' not found.");
        }

        $content = Storage::disk('local')->get($filePath);
        $snapshot = json_decode($content, true);

        if (!$snapshot || !isset($snapshot['data']) || !is_array($snapshot['data'])) {
            throw new \Exception("Invalid backup structure in '{$filename}'.");
        }

        // Execute table restoration with foreign key checks temporarily disabled
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($snapshot['data'] as $table => $rows) {
                if (Schema::hasTable($table)) {
                    DB::table($table)->truncate();
                    if (!empty($rows)) {
                        // Chunk inserts to prevent query size limit issues
                        $chunks = array_chunk($rows, 100);
                        foreach ($chunks as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                }
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            AuditLog::record(
                'backup_restored',
                "Restored database from snapshot '{$filename}'",
                auth()->id(),
                ['filename' => $filename],
                'warning'
            );

            return true;
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            throw $e;
        }
    }

    /**
     * Delete a backup file.
     */
    public static function deleteBackup(string $filename): bool
    {
        $filename = basename($filename);
        $filePath = self::$backupDir . '/' . $filename;

        if (Storage::disk('local')->exists($filePath)) {
            Storage::disk('local')->delete($filePath);

            AuditLog::record(
                'backup_deleted',
                "Deleted backup file '{$filename}'",
                auth()->id(),
                ['filename' => $filename],
                'info'
            );

            return true;
        }

        return false;
    }

    /**
     * Get absolute path for downloading a backup.
     */
    public static function getBackupDownloadPath(string $filename): string
    {
        $filename = basename($filename);
        $filePath = self::$backupDir . '/' . $filename;

        if (!Storage::disk('local')->exists($filePath)) {
            throw new \Exception("Backup file not found.");
        }

        return Storage::disk('local')->path($filePath);
    }

    /**
     * Format bytes into human readable format.
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
