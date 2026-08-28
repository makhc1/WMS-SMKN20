<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Maintenance', [
            'settings' => SystemSetting::getMaintenanceDetails(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'is_active' => 'required|boolean',
            'message' => 'nullable|string|max:500',
            'estimated_finish' => 'nullable|string|max:100',
        ]);

        SystemSetting::set('maintenance_mode', (bool) $validated['is_active']);
        
        if (!empty($validated['message'])) {
            SystemSetting::set('maintenance_message', $validated['message']);
        } else {
            SystemSetting::set('maintenance_message', 'Sistem sedang dalam proses pemeliharaan berkala untuk peningkatan performa.');
        }

        SystemSetting::set('maintenance_estimated_finish', $validated['estimated_finish'] ?? null);
        SystemSetting::set('maintenance_updated_at', now()->isoFormat('D MMMM Y, HH:mm'));
        SystemSetting::set('maintenance_updated_by_name', auth()->user()->name);

        $statusText = $validated['is_active'] ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('message', "Mode pemeliharaan berhasil {$statusText}.");
    }
}
