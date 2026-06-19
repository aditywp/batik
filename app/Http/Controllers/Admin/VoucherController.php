<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->get();
        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        return view('admin.vouchers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|max:50|unique:vouchers,code',
            'discount_amount' => 'required|numeric|min:0',
            'points_required' => 'required|integer|min:0',
            'valid_until'     => 'nullable|date',
        ]);

        Voucher::create([
            'name'               => $request->name,
            'code'               => strtoupper($request->code),
            'discount_amount'    => $request->discount_amount,
            'points_required'    => $request->points_required,
            'is_welcome_voucher' => $request->has('is_welcome_voucher'),
            'is_active'          => $request->has('is_active'),
            'valid_until'        => $request->valid_until,
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil ditambahkan!');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => 'required|string|max:50|unique:vouchers,code,' . $voucher->id,
            'discount_amount' => 'required|numeric|min:0',
            'points_required' => 'required|integer|min:0',
            'valid_until'     => 'nullable|date',
        ]);

        $voucher->update([
            'name'               => $request->name,
            'code'               => strtoupper($request->code),
            'discount_amount'    => $request->discount_amount,
            'points_required'    => $request->points_required,
            'is_welcome_voucher' => $request->has('is_welcome_voucher'),
            'is_active'          => $request->has('is_active'),
            'valid_until'        => $request->valid_until,
        ]);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil diperbarui!');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher berhasil dihapus!');
    }
}