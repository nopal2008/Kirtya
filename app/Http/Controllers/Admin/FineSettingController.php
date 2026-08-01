<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FineSetting;
use Illuminate\Http\Request;

class FineSettingController extends Controller
{
    public function edit()
    {
        $setting = FineSetting::getActive() ?? new FineSetting([
            'daily_rate'          => 1000,
            'max_borrow_days'     => 7,
            'max_borrow_limit'    => 3,
            'damage_fee'          => 50000,
            'lost_fee_multiplier' => 2.00,
        ]);

        return view('admin.fines.settings', compact('setting'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'daily_rate'          => ['required', 'numeric', 'min:0'],
            'max_borrow_days'     => ['required', 'integer', 'min:1', 'max:60'],
            'max_borrow_limit'    => ['required', 'integer', 'min:1', 'max:10'],
            'damage_fee'          => ['required', 'numeric', 'min:0'],
            'lost_fee_multiplier' => ['required', 'numeric', 'min:1', 'max:10'],
        ]);

        // Non-aktifkan setting lama
        FineSetting::where('is_active', true)->update(['is_active' => false]);

        // Buat setting baru
        FineSetting::create([
            'daily_rate'          => $validated['daily_rate'],
            'max_borrow_days'     => $validated['max_borrow_days'],
            'max_borrow_limit'    => $validated['max_borrow_limit'],
            'damage_fee'          => $validated['damage_fee'],
            'lost_fee_multiplier' => $validated['lost_fee_multiplier'],
            'is_active'           => true,
            'created_by'          => auth()->id(),
        ]);

        return redirect()->route('admin.fines.settings')
            ->with('success', 'Konfigurasi denda dan peminjaman berhasil diperbarui.');
    }
}
