<?php

namespace App\Http\Controllers;

use App\Http\Requests\Backup\RestoreBackupRequest;
use App\Services\DatabaseBackupService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function __construct(private DatabaseBackupService $service)
    {
        $this->middleware('permission:backup.view')->only(['index']);
        $this->middleware('permission:backup.create')->only(['store']);
        $this->middleware('permission:backup.restore')->only(['restore']);
        $this->middleware('permission:backup.delete')->only(['destroy']);
        $this->middleware('permission:backup.view')->only(['download']);
    }

    public function index(): Response
    {
        return Inertia::render('Backups/Index', [
            'backups' => $this->service->list(),
            'backupPath' => $this->service->backupPath(),
            'databaseDriver' => config('database.default'),
        ]);
    }

    public function store(): RedirectResponse
    {
        $this->service->create();

        return redirect()->route('backups.index')->with('success', __('erp.backup.created'));
    }

    public function download(string $backup): BinaryFileResponse
    {
        $path = $this->service->resolveBackupFile($backup);

        return response()->download($path, basename($path));
    }

    public function restore(RestoreBackupRequest $request): RedirectResponse
    {
        $zipPath = $request->hasFile('backup_file')
            ? $request->file('backup_file')->getRealPath()
            : $this->service->resolveBackupFile($request->validated('filename'));

        $this->service->restore($zipPath);

        return redirect()->route('backups.index')->with('success', __('erp.backup.restored'));
    }

    public function destroy(string $backup): RedirectResponse
    {
        $this->service->delete($backup);

        return redirect()->route('backups.index')->with('success', __('erp.backup.deleted'));
    }
}
