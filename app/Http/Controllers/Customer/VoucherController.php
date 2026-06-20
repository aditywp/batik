<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // PERBAIKAN 1: Ambil tanggal hari ini saja tanpa jam (format YYYY-MM-DD)
        $today = Carbon::now('Asia/Jakarta')->toDateString(); 
        
        // Master voucher untuk ditukar (Redeem)
        $vouchers = Voucher::where('is_active', true)
            ->where('is_welcome_voucher', false)
            ->where(function($query) use ($today) {
                $query->whereNull('valid_until')
                      // Menggunakan orWhereDate agar sistem mengabaikan jam 00:00:00 di database
                      ->orWhereDate('valid_until', '>=', $today); 
            })
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
        $now = Carbon::now('Asia/Jakarta');

        // PERBAIKAN 2: Paksa tanggal kedaluwarsa ke ujung hari (jam 23:59:59) sebelum dibandingkan
        $isExpired = $voucher->valid_until && Carbon::parse($voucher->valid_until, 'Asia/Jakarta')->endOfDay()->lessThan($now);

        // 1. Validasi Status dan Kedaluwarsa
        if (!$voucher->is_active || $isExpired) {
            return redirect()->back()->with('error', 'Maaf, voucher ini sudah tidak aktif atau telah kedaluwarsa.');
        }

        // 2. Validasi Duplikasi (Keamanan Tambahan)
        // Mengecek apakah user sudah menukar voucher ini dan belum memakainya
        $alreadyHasVoucher = $user->vouchers()->where('voucher_id', $voucher->id)->wherePivot('is_used', false)->exists();
        if ($alreadyHasVoucher) {
             return redirect()->back()->with('error', 'Anda sudah menukarkan voucher ini dan belum menggunakannya.');
        }

        // 3. Validasi Ketersediaan Poin
        if ($user->points < $voucher->points_required) {
            return redirect()->back()->with('error', 'Poin Anda tidak cukup untuk menukar voucher ini.');
        }

        // 4. Proses Penukaran Poin
        $user->decrement('points', $voucher->points_required);
        $user->vouchers()->attach($voucher->id);

        return redirect()->back()->with('success', "Berhasil menukarkan poin dengan: {$voucher->name}!");
    }
}