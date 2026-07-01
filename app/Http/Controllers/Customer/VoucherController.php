<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\UserVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::now('Asia/Jakarta')->toDateString(); 
        
        // Katalog voucher aktif yang bisa ditukar
        $vouchers = Voucher::where('is_active', true)
            ->where('is_welcome_voucher', false)
            ->where(function($query) use ($today) {
                $query->whereNull('valid_until')
                      ->orWhereDate('valid_until', '>=', $today); 
            })
            ->get();

        // Mengambil langsung dari dompet user menggunakan Model UserVoucher baru
        $allMyVouchers = UserVoucher::with('voucher')
            ->where('user_id', $user->id)
            ->latest()
            ->get();
        
        $availableVouchers = $allMyVouchers->where('is_used', false);
        $usedVouchers = $allMyVouchers->where('is_used', true);

        return view('customer.vouchers.index', compact('vouchers', 'availableVouchers', 'usedVouchers', 'user'));
    }

    public function redeem(Voucher $voucher)
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $isExpired = $voucher->valid_until && Carbon::parse($voucher->valid_until, 'Asia/Jakarta')->endOfDay()->lessThan($now);

        if (!$voucher->is_active || $isExpired) {
            return redirect()->back()->with('error', 'Maaf, voucher ini sudah tidak aktif atau telah kedaluwarsa.');
        }

        $alreadyHasVoucher = UserVoucher::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->where('is_used', false)
            ->exists();

        if ($alreadyHasVoucher) {
             return redirect()->back()->with('error', 'Anda sudah menukarkan voucher ini dan belum menggunakannya.');
        }

        if ($user->points < $voucher->points_required) {
            return redirect()->back()->with('error', 'Poin Anda tidak cukup untuk menukar voucher ini.');
        }

        // Jalankan pemotongan poin
        $user->decrement('points', $voucher->points_required);
        
        // Simpan snapshot ke dompet secara permanen
        UserVoucher::create([
            'user_id'           => $user->id,
            'voucher_id'        => $voucher->id,
            'code_snapshot'     => $voucher->code,
            'discount_snapshot' => $voucher->discount_amount,
            'is_used'           => false
        ]);

        return redirect()->back()->with('success', "Berhasil menukarkan poin dengan: {$voucher->name}!");
    }
}