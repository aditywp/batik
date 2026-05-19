<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCharts;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;

class OrdersExport implements FromCollection, WithHeadings, WithStyles, WithCharts
{
    public function collection()
    {
        return Order::with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($order) {
                return [
                    $order->order_code,
                    $order->user->name ?? 'Umum',
                    $order->user->email ?? '-',
                    $order->total,
                    strtoupper($order->status),
                    strtoupper($order->payment_status),
                    $order->created_at->format('d/m/Y H:i')
                ];
            });
    }

    public function headings(): array
    {
        return ['Kode Order', 'Pelanggan', 'Email', 'Total', 'Status', 'Pembayaran', 'Tanggal'];
    }

    public function styles(Worksheet $sheet)
    {
        // Lebar kolom otomatis agar tidak kepotong (terhindar dari error ###)
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Font mewah dasar
        $sheet->getParent()->getDefaultStyle()->getFont()->setName('Plus Jakarta Sans');

        // Style Header Utama (Navy #1a1a2e & Teks Emas/Putih)
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1A1A2E']
            ],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        // Beri format rupiah pada kolom Total (Kolom D)
        $rowCount = $sheet->getHighestRow();
        $sheet->getStyle('D2:D' . $rowCount)->getNumberFormat()->setFormatCode('Rp #,##0');
        
        // Tambahkan border tipis estetik pada tabel data
        $sheet->getStyle('A1:G' . $rowCount)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    }

    public function charts(): array
    {
        // Membuat grafik otomatis di bawah baris data
        $rowCount = 11; 
        $labels = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_STRING, 'Worksheet!$A$2:$A$'.$rowCount, null, $rowCount - 1)];
        $values = [new DataSeriesValues(DataSeriesValues::DATASERIES_TYPE_NUMBER, 'Worksheet!$D$2:$D$'.$rowCount, null, $rowCount - 1)];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );

        $plot = new PlotArea(null, [$series]);
        $chart = new Chart('chart1', new Title('Tren Transaksi Penjualan'), null, $plot);
        $chart->setTopLeftPosition('A' . ($rowCount + 3));
        $chart->setBottomRightPosition('G' . ($rowCount + 18));

        return [$chart];
    }
}