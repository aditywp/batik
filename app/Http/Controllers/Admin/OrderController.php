<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// IMPORT LIBRARY PHPSPREADSHEET UNTUK FORMAT EXCEL PREMIUM
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class OrderController extends Controller
{
    /**
     * Daftar semua pesanan dengan filter & search + Integrasi Otomatisasi Sinkronisasi Midtrans
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items'])
            ->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan status pembayaran
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search berdasarkan kode order atau nama customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%")
                                                     ->orWhere('email', 'like', "%{$search}%"));
            });
        }

        // Ambil data pesanan terpaginasi (15 baris data)
        $orders = $query->paginate(15)->withQueryString();

        // ======================================================================
        // AUTOMATIC REAL-TIME SYNC FOR ADMIN INDEX (MASS PULL SYSTEM)
        // Memeriksa dan memperbarui status pesanan secara real-time saat admin membuka dashboard
        // ======================================================================
        foreach ($orders as $order) {
            if ($order->payment_status === 'unpaid' || $order->payment_status === 'pending') {
                try {
                    \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                    \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

                    // Ambil status transaksi resmi langsung dari Core API Midtrans Sandbox
                    $status = \Midtrans\Transaction::status($order->order_code);
                    $midtransStatus = $status->transaction_status;

                    if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                        $order->update([
                            'payment_status'          => 'cancelled',
                            'status'                  => 'cancelled',
                            'midtrans_transaction_id' => $status->transaction_id ?? null,
                            'payment_method'          => $status->payment_type ?? null,
                        ]);
                    } elseif ($midtransStatus == 'settlement' || $midtransStatus == 'capture') {
                        $order->update([
                            'payment_status'          => 'paid',
                            'status'                  => 'processing',
                            'midtrans_transaction_id' => $status->transaction_id ?? null,
                            'payment_method'          => $status->payment_type ?? null,
                            'paid_at'                 => now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    // Abaikan jika token/invoice belum dibuat sama sekali di server Midtrans
                }
            }
        }

        // Hitung ringkasan untuk stat cards (Mengambil data mutasi pasca-sinkronisasi)
        $summary = [
            'total'      => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'delivered'  => Order::where('status', 'delivered')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'summary'));
    }

    /**
     * Halaman Laporan Penjualan Dinamis Dashboard Admin (Filter Tahun & Bulan Berbasis Dropdown)
     */
    public function report(Request $request)
    {
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month'); 

        $baseQuery = Order::where('payment_status', 'paid');

        if ($request->filled('month')) {
            // STRATEGI A: JIKA FILTER PER BULAN DIPILIH -> AGREGASI TREN TANGGAL HARIAN
            $baseQuery->whereYear('created_at', $selectedYear)
                       ->whereMonth('created_at', $selectedMonth);

            // PAGINASI DIKUNCI KE 10 BARIS DEMI ESTETIKA DAN SCANNABILITY UI
            $orders = $baseQuery->latest()->paginate(10)->withQueryString();
            $totalRevenue = $baseQuery->sum('total');
            $totalTransactions = $baseQuery->count();

            $chartDataRaw = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->selectRaw('DATE(created_at) as date_label, SUM(total) as amount')
                ->groupBy('date_label')
                ->orderBy('date_label', 'asc')
                ->get();

            $chartLabels = $chartDataRaw->pluck('date_label')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'));
            $chartData = $chartDataRaw->pluck('amount');
            
            $namaBulanIndo = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];
            $chartTitle = "Grafik Tren Penjualan Harian - Periode " . $namaBulanIndo[$selectedMonth] . " " . $selectedYear;
        } else {
            // STRATEGI B: JIKA HANYA FILTER TAHUN YANG DIPILIH -> REKAP AGREGASI BULANAN KRONOLOGIS KONTINU
            $baseQuery->whereYear('created_at', $selectedYear);

            // PAGINASI DIKUNCI KE 10 BARIS DEMI ESTETIKA DAN SCANNABILITY UI
            $orders = $baseQuery->latest()->paginate(10)->withQueryString();
            $totalRevenue = $baseQuery->sum('total');
            $totalTransactions = $baseQuery->count();

            $chartDataRaw = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $selectedYear)
                ->selectRaw('MONTH(created_at) as month_num, SUM(total) as amount')
                ->groupBy('month_num')
                ->orderBy('month_num', 'asc')
                ->get();

            $monthNamesArr = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
            ];

            $chartLabels = $chartDataRaw->pluck('month_num')->map(fn($m) => $monthNamesArr[$m] ?? $m);
            $chartData = $chartDataRaw->pluck('amount');
            $chartTitle = "Grafik Perbandingan Omset Bulanan - Tahun " . $selectedYear;
        }

        // MENYEDIAKAN VARIABEL $availableYears AGAR TIDAK UNDEFINED LAGI DI VIEW BLADE
        $availableYears = Order::selectRaw('YEAR(created_at) as year')->distinct()->orderBy('year', 'desc')->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([now()->year]);
        }

        return view('admin.orders.report', compact(
            'orders', 'totalRevenue', 'totalTransactions', 
            'chartLabels', 'chartData', 'chartTitle', 
            'selectedYear', 'selectedMonth', 'availableYears'
        ));
    }

    /**
     * Export Excel Dinamis (.xlsx) Menyesuaikan Filter Dropdown Tahun & Bulan
     */
    public function exportExcel(Request $request)
    {
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month');

        $allOrdersQuery = Order::with(['items'])->where('payment_status', 'paid');
        if ($request->filled('month')) {
            $allOrdersQuery->whereYear('created_at', $selectedYear)->whereMonth('created_at', $selectedMonth);
        } else {
            $allOrdersQuery->whereYear('created_at', $selectedYear);
        }
        $allOrders = $allOrdersQuery->get();

        $totalItemsSold = 0;
        foreach($allOrders as $o) {
            $totalItemsSold += $o->items->sum('quantity');
        }

        $spreadsheet = new Spreadsheet();
        
        // --- SHEET 1: DASHBOARD ---
        $wsDash = $spreadsheet->getActiveSheet();
        $wsDash->setTitle('Dashboard Analisis');
        $wsDash->setShowGridlines(true);
        $wsDash->getParent()->getDefaultStyle()->getFont()->setName('Plus Jakarta Sans');

        $wsDash->setCellValue('B2', 'BATIK IFAWATI - MANAGEMENT DATA REPORT');
        $wsDash->getStyle('B2')->getFont()->setSize(16)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1A1A2E'));
        
        $fillLightNavy = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0F2F5']];
        $thinBorder = ['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]]];

        // --- SHEET 2: DATA SOURCE ---
        $wsData = $spreadsheet->createSheet();
        $wsData->setTitle('Data_Sumber');
        $wsData->setShowGridlines(true);

        if ($request->filled('month')) {
            $wsDash->setCellValue('B3', 'Laporan Analisis Grafik Tren Transaksi Harian Finansial Toko Secara Real-Time');
            
            $dataColumns = ['Tanggal', 'Total Omset Harian', 'Jumlah Transaksi'];
            foreach ($dataColumns as $idx => $name) { $wsData->setCellValue(chr(65 + $idx) . '1', $name); }

            $salesData = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->selectRaw('DATE(created_at) as label, SUM(total) as total_amount, COUNT(id) as trans_count')
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        } else {
            $wsDash->setCellValue('B3', 'Laporan Analisis Grafik Perbandingan Omset Bulanan Finansial Toko Secara Real-Time');
            
            $dataColumns = ['Bulan / Periode', 'Total Omset Bulanan', 'Jumlah Transaksi'];
            foreach ($dataColumns as $idx => $name) { $wsData->setCellValue(chr(65 + $idx) . '1', $name); }

            $salesDataRaw = Order::where('payment_status', 'paid')
                ->whereYear('created_at', $selectedYear)
                ->selectRaw('MONTH(created_at) as month_num, SUM(total) as total_amount, COUNT(id) as trans_count')
                ->groupBy('month_num')
                ->orderBy('month_num', 'asc')
                ->get();

            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
                7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ];

            $salesData = $salesDataRaw->map(function($item) use ($monthNames, $selectedYear) {
                return (object)[
                    'label' => $monthNames[$item->month_num] . ' ' . $selectedYear,
                    'total_amount' => $item->total_amount,
                    'trans_count' => $item->trans_count
                ];
            });
        }

        $rowIdx = 2;
        foreach ($salesData as $item) {
            $wsData->setCellValue('A' . $rowIdx, $item->label);
            $wsData->setCellValue('B' . $rowIdx, $item->total_amount);
            $wsData->setCellValue('C' . $rowIdx, $item->trans_count);
            
            if ($rowIdx % 2 == 0) {
                $wsData->getStyle('A'.$rowIdx.':C'.$rowIdx)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F7F9FA');
            }
            $rowIdx++;
        }
        $maxRow = $rowIdx - 1;

        if ($salesData->isEmpty()) {
            $wsData->setCellValue('A2', $selectedYear);
            $wsData->setCellValue('B2', 'Tidak ada transaksi');
            $wsData->setCellValue('C2', 0);
            $wsData->setCellValue('D2', 0);
            $maxRow = 2;
        }

        $wsData->getStyle('A1:C1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A1A2E']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $wsData->getStyle('B2:B' . $maxRow)->getNumberFormat()->setFormatCode('Rp #,##0');
        $wsData->getStyle('C2:C' . $maxRow)->getNumberFormat()->setFormatCode('#,##0');
        $wsData->getStyle('A1:C' . $maxRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E5E7EB');
        $wsData->getStyle('A2:A' . $maxRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsData->getStyle('C2:C' . $maxRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- 4 KOTAK KPI STAT CARDS ---
        $wsDash->setCellValue('B5', 'TOTAL PENDAPATAN');
        $wsDash->setCellValue('B6', '=SUM(Data_Sumber!B2:B' . $maxRow . ')');
        $wsDash->getStyle('B6')->getNumberFormat()->setFormatCode('Rp #,##0');

        $wsDash->setCellValue('D5', 'TOTAL TRANSAKSI');
        $wsDash->setCellValue('D6', '=SUM(Data_Sumber!C2:C' . $maxRow . ')');
        $wsDash->getStyle('D6')->getNumberFormat()->setFormatCode('#,##0');

        $wsDash->setCellValue('F5', 'RATA-RATA OMSET');
        $wsDash->setCellValue('F6', '=AVERAGE(Data_Sumber!B2:B' . $maxRow . ')');
        $wsDash->getStyle('F6')->getNumberFormat()->setFormatCode('Rp #,##0');

        $wsDash->setCellValue('H5', 'VOLUME PRODUK TERJUAL');
        $wsDash->setCellValue('H6', $totalItemsSold);
        $wsDash->getStyle('H6')->getNumberFormat()->setFormatCode('#,##0 "Pcs"');

        foreach (['B', 'D', 'F', 'H'] as $col) {
            $wsDash->getStyle($col.'5')->getFont()->setSize(8.5)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('6B7280'));
            $wsDash->getStyle($col.'5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $wsDash->getStyle($col.'5')->applyFromArray($fillLightNavy)->applyFromArray($thinBorder);

            $wsDash->getStyle($col.'6')->getFont()->setSize(14)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1A1A2E'));
            $wsDash->getStyle($col.'6')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $wsDash->getStyle($col.'6')->applyFromArray($fillLightNavy)->applyFromArray($thinBorder);
        }

        // --- SHEET 3: DATA INTEGRASI STOK PRODUK ---
        $wsStock = $spreadsheet->createSheet();
        $wsStock->setTitle('Manajemen_Stok');
        $wsStock->setShowGridlines(true);

        $stockHeaders = ['ID Produk', 'Nama Kain Batik', 'Sisa Stok di Gudang', 'Status Ketersediaan'];
        foreach ($stockHeaders as $idx => $headerName) {
            $wsStock->setCellValue(chr(65 + $idx) . '1', $headerName);
        }

        $allProducts = Product::all();
        $stockRow = 2;
        foreach($allProducts as $prod) {
            $wsStock->setCellValue('A' . $stockRow, $prod->id);
            $wsStock->setCellValue('B' . $stockRow, $prod->name);
            $wsStock->setCellValue('C' . $stockRow, $prod->stock);
            $wsStock->setCellValue('D' . $stockRow, '=IF(C'.$stockRow.'<=5, "⚠️ RESTOCK SEGERA", "✅ STOK AMAN")');
            
            if ($stockRow % 2 == 0) {
                $wsStock->getStyle('A'.$stockRow.':D'.$stockRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FAF9F6');
            }
            $stockRow++;
        }
        $maxStockRow = $stockRow - 1;
        $wsStock->getStyle('A1:D1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E1A1A']], 
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);
        $wsStock->getStyle('A1:D' . $maxStockRow)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('E5E7EB');
        $wsStock->getStyle('A2:A' . $maxStockRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $wsStock->getStyle('C2:D' . $maxStockRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // --- SHEET 1 UPDATED: MEMBUAT CHART DINAMIS ---
        if ($maxRow >= 2 && !$salesData->isEmpty()) {
            $dataLabels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Data_Sumber!$A$2:$A$' . $maxRow, null, $maxRow - 1)];
            $dataValues = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Data_Sumber!$B$2:$B$' . $maxRow, null, $maxRow - 1)];

            $chartType = $request->filled('month') ? DataSeries::TYPE_LINECHART : DataSeries::TYPE_BARCHART;
            $titleText = $request->filled('month') ? 'Grafik Fluktuasi Omset Pendapatan Harian' : 'Grafik Perbandingan Pertumbuhan Omset Antar Bulan';

            $series = new DataSeries(
                $chartType,
                DataSeries::GROUPING_STANDARD,
                range(0, count($dataValues) - 1),
                [],
                $dataLabels,
                $dataValues
            );

            $plotArea = new PlotArea(null, [$series]);
            $chart = new Chart('dynamic_chart', new Title($titleText . ' (IDR)'), null, $plotArea);
            
            $chart->setTopLeftPosition('B9');
            $chart->setBottomRightPosition('I26');
            $wsDash->addChart($chart);
        }

        foreach ($wsData->getColumnIterator() as $col) { $wsData->getColumnDimension($col->getColumnIndex())->setAutoSize(true); }
        foreach ($wsStock->getColumnIterator() as $col) { $wsStock->getColumnDimension($col->getColumnIndex())->setAutoSize(true); }
        foreach (range('A', 'J') as $colLetter) { $wsDash->getColumnDimension($colLetter)->setWidth(20); }
        $wsDash->getColumnDimension('A')->setWidth(4);

        $periodName = $request->filled('month') ? 'bulan-'.$selectedMonth.'-' : 'tahun-';
        $filename = 'laporan-omset-' . $periodName . $selectedYear . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->setIncludeCharts(true); 
        $writer->save('php://output');
        exit;
    }

    /**
     * Detail satu pesanan + Otomatisasi Real-time Sinkronisasi Midtrans
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product.images', 'items.variant']);

        // ======================================================================
        // AUTOMATIC REAL-TIME SYNC FOR ADMIN SHOW (PULL SYSTEM)
        // Memeriksa dan memperbarui status pesanan saat admin membuka lembar detail nota
        // ======================================================================
        if ($order->payment_status === 'unpaid' || $order->payment_status === 'pending') {
            try {
                \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                \Midtrans\Config::$isProduction = config('services.midtrans.is_production', false);

                // Tarik mutasi status resmi langsung dari Core API Midtrans Sandbox
                $status = \Midtrans\Transaction::status($order->order_code);
                $midtransStatus = $status->transaction_status;

                if (in_array($midtransStatus, ['expire', 'cancel', 'deny'])) {
                    $order->update([
                        'payment_status'          => 'cancelled',
                        'status'                  => 'cancelled',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                        'payment_method'          => $status->payment_type ?? null,
                    ]);
                } elseif ($midtransStatus == 'settlement' || $midtransStatus == 'capture') {
                    $order->update([
                        'payment_status'          => 'paid',
                        'status'                  => 'processing',
                        'midtrans_transaction_id' => $status->transaction_id ?? null,
                        'payment_method'          => $status->payment_type ?? null,
                        'paid_at'                 => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Abaikan jika order belum terdaftar di server sandbox Midtrans
            }
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status pesanan, Input Resi, & Sinkronisasi Stok
     */
    public function updateStatus(Request $request, Order $order)
    {
        $allowed = method_exists($order, 'allowedNextStatuses') 
            ? $order->allowedNextStatuses() 
            : ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        $request->validate([
            'status' => ['required', 'in:' . implode(',', $allowed)],
            'tracking_number' => $request->status === 'shipped' ? 'required|string|max:100' : 'nullable',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        DB::transaction(function () use ($order, $oldStatus, $newStatus, $request) {
            $updateData = ['status' => $newStatus];

            if ($request->filled('tracking_number')) {
                $updateData['tracking_number'] = $request->tracking_number;
            }

            $order->update($updateData);

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        $variant = ProductVariant::find($item->variant_id);
                        if ($variant) {
                            $variant->increment('stock', $item->quantity);
                        }
                    }

                    $product = Product::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }
        });

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', "Status pesanan berhasil diperbarui.");
    }

    /**
     * Export daftar order umum ke CSV
     */
    public function export(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        $filename = 'orders-list-' . now()->format('Ymd') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $columns = ['Kode Order', 'Pelanggan', 'Email', 'Total', 'Status', 'Pembayaran', 'Tanggal'];

        $callback = function () use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->user->name,
                    $order->user->email,
                    $order->total,
                    strtoupper($order->status),
                    strtoupper($order->payment_status),
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}