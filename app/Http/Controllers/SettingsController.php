<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Setting::all();
        return view('settings.index', compact('settings'));
    }

    public function currency()
    {
        $settings = Setting::whereIn('key', [
            'currency_code',
            'currency_symbol',
            'currency_position',
            'currency_symbols'
        ])->get();

        return view('settings.currency', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.currency_code' => 'sometimes|required|string|size:3',
            'settings.currency_symbol' => 'sometimes|required|string',
            'settings.currency_position' => 'sometimes|required|in:before,after',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully');
    }
}
