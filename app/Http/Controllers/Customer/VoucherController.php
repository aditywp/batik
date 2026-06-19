<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Master voucher untuk ditukar (Redeem)
        $vouchers = Voucher::where('is_active', true)
            ->where('is_welcome_voucher', false)
            ->get();

        // Ambil semua voucher user
        $allMyVouchers = $user->vouchers()->withPivot('is_used', 'used_at')->latest()->get();
        
        // Pisahkan menjadi 2 kategori
        $availableVouchers = $allMyVouchers->where('pivot.is_used', false);
        $usedVouchers = $allMyVouchers->where('pivot.is_used', true);

        return view('customer.vouchers.index', compact('vouchers', 'availableVouchers', 'usedVouchers', 'user'));
    }

    public function redeem(Voucher $voucher)
    {
        $user = Auth::user();

        if (!$voucher->is_active) {
            return redirect()->back()->with('error', 'Voucher ini sudah tidak aktif.');
        }

        if ($user->points < $voucher->points_required) {
            return redirect()->back()->with('error', 'Poin Anda tidak cukup.');
        }

        $user->decrement('points', $voucher->points_required);
        $user->vouchers()->attach($voucher->id);

        return redirect()->back()->with('success', "Berhasil menukarkan poin dengan: {$voucher->name}!");
    }
}