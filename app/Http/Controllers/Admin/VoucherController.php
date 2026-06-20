<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Carbon\Carbon;

class VoucherController extends Controller
{
    public function index(Request $request)
    {
        $query = Voucher::query()->latest();

        // 1. Fitur Pencarian Nama / Kode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // 2. Fitur Filter 3 Status (Aktif, Nonaktif, Kedaluwarsa)
        if ($request->filled('status')) {
            $today = Carbon::now('Asia/Jakarta')->toDateString();
            
            if ($request->status === 'active') {
                $query->where('is_active', true)
                      ->where(function($q) use ($today) {
                          $q->whereNull('valid_until')
                            ->orWhereDate('valid_until', '>=', $today);
                      });
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false)
                      ->where(function($q) use ($today) {
                          $q->whereNull('valid_until')
                            ->orWhereDate('valid_until', '>=', $today);
                      });
            } elseif ($request->status === 'expired') {
                $query->whereNotNull('valid_until')
                      ->whereDate('valid_until', '<', $today);
            }
        }

        $vouchers = $query->paginate(10)->withQueryString();
        
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        // PERBAIKAN: Penambahan alpha_dash untuk larang spasi & max:9999999 untuk nominal jutaan
        $request->validate([
            'name'            => 'required|string|max:100',
            'code'            => 'required|string|min:3|max:30|alpha_dash|unique:vouchers,code',
            'discount_amount' => 'required|numeric|min:0|max:9999999',
            'points_required' => 'required|integer|min:0|max:10000',
            'valid_until'     => 'nullable|date',
        ], [
            // Pesan Error Kustom agar Admin lebih paham
            'code.alpha_dash'     => 'Kode voucher tidak boleh mengandung spasi. Hanya boleh huruf, angka, strip (-), dan garis bawah (_).',
            'discount_amount.max' => 'Nominal potongan maksimal adalah Rp 9.999.999.',
        ]);

        $validUntil = $request->valid_until 
            ? Carbon::parse($request->valid_until, 'Asia/Jakarta')->endOfDay() 
            : null;

        Voucher::create([
            'name'               => $request->name,
            'code'               => strtoupper($request->code),
            'discount_amount'    => $request->discount_amount,
            'points_required'    => $request->points_required,
            'is_welcome_voucher' => $request->has('is_welcome_voucher'),
            'is_active'          => $request->has('is_active'),
            'valid_until'        => $validUntil,
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil ditambahkan!');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        // PERBAIKAN: Penambahan alpha_dash untuk larang spasi & max:9999999 untuk nominal jutaan
        $request->validate([
            'name'            => 'required|string|max:100',
            'code'            => 'required|string|min:3|max:30|alpha_dash|unique:vouchers,code,' . $voucher->id,
            'discount_amount' => 'required|numeric|min:0|max:9999999',
            'points_required' => 'required|integer|min:0|max:10000',
            'valid_until'     => 'nullable|date',
        ], [
            // Pesan Error Kustom agar Admin lebih paham
            'code.alpha_dash'     => 'Kode voucher tidak boleh mengandung spasi. Hanya boleh huruf, angka, strip (-), dan garis bawah (_).',
            'discount_amount.max' => 'Nominal potongan maksimal adalah Rp 9.999.999.',
        ]);

        $validUntil = $request->valid_until 
            ? Carbon::parse($request->valid_until, 'Asia/Jakarta')->endOfDay() 
            : null;

        $voucher->update([
            'name'               => $request->name,
            'code'               => strtoupper($request->code),
            'discount_amount'    => $request->discount_amount,
            'points_required'    => $request->points_required,
            'is_welcome_voucher' => $request->has('is_welcome_voucher'),
            'is_active'          => $request->has('is_active'),
            'valid_until'        => $validUntil,
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }
}