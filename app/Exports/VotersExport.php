<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Contracts\View\View;

class VotersExport implements FromView, WithEvents
{
    protected $data;
    protected $columns;

    public function __construct($data, $columns)
    {
        $this->data = $data;
        $this->columns = $columns;
    }

    public function view(): View
    {
        return view('dashboard.exports.excel', [
            'voters' => $this->data,
            'columns' => $this->columns
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setRightToLeft(true);

                // Optional: Align text to the right for RTL languages
                $highestRow = $sheet->getHighestRow(); // e.g. 10
                $highestColumn = $sheet->getHighestColumn(); // e.g 'F'
                $range = 'A1:' . $highestColumn . $highestRow; // e.g 'A1:F10'
                $sheet->getStyle($range)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
