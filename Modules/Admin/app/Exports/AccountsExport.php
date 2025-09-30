<?php

namespace Modules\Admin\Exports;

use Modules\Admin\Entities\Account;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class AccountsExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $request;
    protected $totalIncome = 0;
    protected $totalExpense = 0;
    protected $accounts;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Account::with('category')
            ->orderBy('created_at', 'desc');

        // Apply date range filter
        $startDate = $this->request->start_date ?? null;
        $endDate = $this->request->end_date ?? null;

        if ($startDate || $endDate) {
            $query->whereBetween('created_at', [
                $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::minValue(),
                $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::maxValue(),
            ]);
        }

        // Apply search filter
        if ($this->request->search) {
            $query->where(function($q) {
                $q->where('note', 'like', '%'.$this->request->search.'%')
                  ->orWhereHas('category', function($subQ) {
                      $subQ->where('name', 'like', '%'.$this->request->search.'%');
                  });
            });
        }

        // Apply type filter
        if (!empty($this->request->type)) {
            $query->whereIn('type', $this->request->type);
        }

        $this->accounts = $query->get();

        // Calculate totals
        $this->totalIncome = $this->accounts->where('type', 1)->sum('totalAmount');
        $this->totalExpense = $this->accounts->where('type', 2)->sum('totalAmount');

        // Format data for export
        $exportData = $this->accounts->map(function ($account) {
            return [
                'category' => $account->category->name ?? 'N/A',
                'number_ticket' => $account->number_ticket ?? '-',
                'ticket_price' => $account->ticket_price ? '৳' . number_format($account->ticket_price, 2) : '-',
                'income' => $account->type == 1 ? '৳' . number_format($account->totalAmount, 2) : '0',
                'expense' => $account->type == 2 ? '৳' . number_format($account->totalAmount, 2) : '0',
                'created_at' => $account->created_at->format('M d, Y H:i'),
                'note' => $account->note ?? '-'
            ];
        });

        // Add summary rows
        $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => '',
            'income' => '',
            'expense' => '',
            'note' => '',
            'created_at' => ''
        ]);
        $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => '',
            'income' => '',
            'expense' => '',
            'note' => '',
            'created_at' => ''
        ]);
        $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => '',
            'income' => '',
            'expense' => '',
            'note' => '',
            'created_at' => ''
        ]);

        $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => 'SUMMARY',
            'income' => '',
            'expense' => '',
            'note' => '',
            'created_at' => ''
        ]);

        $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => 'Total:',
            'income' => '৳' . number_format($this->totalIncome, 2),
            'expense' => '৳' . number_format($this->totalExpense, 2),
            'note' => '',
            'created_at' => ''
        ]);

         $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => '',
            'income' => '',
            'expense' => '',
            'note' => '',
            'created_at' => ''
        ]);

        $profit = $this->totalIncome - $this->totalExpense;
        $exportData->push([
            'category' => '',
            'number_ticket' => '',
            'ticket_price' => 'Result:',
            'income' => '৳' . number_format($profit, 2),
            'expense' => $profit >= 0 ? 'PROFIT' : 'LOSS',
            'note' => '',
            'created_at' => ''
        ]);

        return $exportData;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Category',
            'Number Ticket',
            'Ticket Price',
            'Income',
            'Expense/Maintenance',
            'Created At',
            'Note',
        ];
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                // Style header row
                $sheet->getStyle('A1:' . $highestColumn . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '4472C4']
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]
                    ]
                ]);

                // Style data rows
                $dataRange = 'A2:' . $highestColumn . ($highestRow - 5);
                $sheet->getStyle($dataRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC']
                        ]
                    ]
                ]);

                // Style summary section
                $summaryStartRow = $highestRow - 4;


                // Style total rows
                for ($i = $summaryStartRow + 1; $i <= $highestRow; $i++) {
                    $sheet->getStyle('C' . $i . ':E' . $i)->applyFromArray([
                        'font' => [
                            'bold' => true
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => $i == $highestRow ? 'FFE699' : 'F2F2F2']
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    ]);
                }

                // Center align headers
                $sheet->getStyle('A1:' . $highestColumn . '1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
