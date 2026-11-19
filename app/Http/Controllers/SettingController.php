<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use App\Support\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const KEYS = ['low_stock_threshold', 'late_fine_per_day', 'default_loan_days', 'max_extensions'];

    public function index(): View
    {
        $values = [];
        foreach (self::KEYS as $key) {
            $values[$key] = setting($key);
        }

        return view('settings.index', ['values' => $values]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        foreach (self::KEYS as $key) {
            Setting::put($key, $request->validated()[$key]);
        }

        ActivityLogger::log('update_settings', 'Memperbarui pengaturan sistem (stok menipis, denda, dan perpanjangan).');

        return redirect()->route('admin.settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
