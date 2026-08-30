<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\BackupService;
use App\Services\SecurityService;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    /**
     * Cyber Security Health & Overview Center
     */
    public function index()
    {
        $healthReport = SecurityService::getSecurityHealthReport();
        $recentAuditLogs = AuditLog::with('user')->latest()->take(10)->get();

        return view('admin.security.index', compact('healthReport', 'recentAuditLogs'));
    }

    /**
     * Audit Logs Explorer
     */
    public function auditLogs(Request $request)
    {
        $query = AuditLog::with('user');

        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->input('severity'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ip_address', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $logs = $query->latest()->paginate(15)->withQueryString();
        $eventTypes = AuditLog::select('event')->distinct()->pluck('event');

        return view('admin.security.audit_logs', compact('logs', 'eventTypes'));
    }

    /**
     * Clear Old Audit Logs
     */
    public function clearAuditLogs(Request $request)
    {
        $days = $request->input('days', 30);
        $deleted = AuditLog::where('created_at', '<', now()->subDays($days))->delete();

        AuditLog::record(
            'logs_purged',
            "Purged {$deleted} audit logs older than {$days} days",
            auth()->id(),
            ['days' => $days, 'count' => $deleted],
            'warning'
        );

        return back()->with('success', "Purged {$deleted} older audit log entries.");
    }

    /**
     * Database Backups & Disaster Recovery Center
     */
    public function backups()
    {
        $backups = BackupService::getBackupsList();
        $healthReport = SecurityService::getSecurityHealthReport();

        return view('admin.security.backups', compact('backups', 'healthReport'));
    }

    /**
     * Create a new database backup snapshot
     */
    public function createBackup()
    {
        try {
            $result = BackupService::createBackup();
            return back()->with('success', "Backup snapshot '{$result['filename']}' ({$result['size']}) created successfully!");
        } catch (\Exception $e) {
            return back()->withErrors(['backup' => 'Backup generation failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Download a database backup snapshot
     */
    public function downloadBackup(string $filename)
    {
        try {
            $filePath = BackupService::getBackupDownloadPath($filename);
            return response()->download($filePath, $filename, [
                'Content-Type' => 'application/json',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['backup' => 'Failed to download backup: ' . $e->getMessage()]);
        }
    }

    /**
     * Restore database from snapshot
     */
    public function restoreBackup(Request $request, string $filename)
    {
        try {
            BackupService::restoreBackup($filename);
            return back()->with('success', "Database successfully restored from snapshot '{$filename}'!");
        } catch (\Exception $e) {
            return back()->withErrors(['restore' => 'Restoration failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete backup snapshot
     */
    public function deleteBackup(string $filename)
    {
        if (BackupService::deleteBackup($filename)) {
            return back()->with('success', "Backup '{$filename}' was deleted successfully.");
        }
        return back()->withErrors(['backup' => 'Could not find backup file to delete.']);
    }
}
