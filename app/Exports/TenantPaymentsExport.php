<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TenantPaymentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $tenantId;
    protected $startDate;
    protected $endDate;

    public function __construct($tenantId, $startDate = null, $endDate = null)
    {
        $this->tenantId = $tenantId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Payment::where('tenant_id', $this->tenantId)
            ->with(['unit', 'property', 'mpesaTransaction']);

        if ($this->startDate) {
            $query->whereDate('payment_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('payment_date', '<=', $this->endDate);
        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Payment Date',
            'Property',
            'Unit',
            'Amount (KES)',
            'Payment Method',
            'Payment Type',
            'M-Pesa Receipt',
            'Status',
            'Notes',
            'Recorded By',
            'Created At'
        ];
    }

    /**
     * @param mixed $payment
     * @return array
     */
    public function map($payment): array
    {
        return [
            $payment->payment_date ? $payment->payment_date->format('Y-m-d') : '',
            $payment->property->name ?? 'N/A',
            $payment->unit->unit_number ?? 'N/A',
            number_format($payment->amount, 2),
            ucfirst($payment->payment_method),
            ucfirst($payment->payment_type),
            $payment->mpesaTransaction->mpesa_receipt_number ?? 'N/A',
            'Completed',
            $payment->notes ?? '',
            $payment->recordedBy->name ?? 'System',
            $payment->created_at->format('Y-m-d H:i:s')
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4F46E5'], // Indigo color
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style data rows
        $highestRow = $sheet->getHighestRow();
        if ($highestRow > 1) {
            $sheet->getStyle('A2:K' . $highestRow)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Alternate row colors
            for ($i = 2; $i <= $highestRow; $i++) {
                if ($i % 2 == 0) {
                    $sheet->getStyle('A' . $i . ':K' . $i)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'F8FAFC'], // Light gray
                        ],
                    ]);
                }
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15, // Payment Date
            'B' => 20, // Property
            'C' => 12, // Unit
            'D' => 15, // Amount
            'E' => 15, // Payment Method
            'F' => 15, // Payment Type
            'G' => 20, // M-Pesa Receipt
            'H' => 12, // Status
            'I' => 30, // Notes
            'J' => 20, // Recorded By
            'K' => 20, // Created At
        ];
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return 'Payment History';
    }
}
