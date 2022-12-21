<?php

namespace App\Exports;

use App\Models\DeliveryCsv;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class DeliveryExport implements ShouldAutoSize, FromCollection, WithHeadings, WithStartRow, WithTitle, WithStyles, WithColumnWidths
{
    /**
    * @return \Illuminate\Support\Collection
    */
    use Exportable;
    public function startRow(): int
    {
        return 2;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true],['size' => 16]],
            2    => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'k' => 55,
        ];
    }

    public function title() : String
    {
        $dateMonth = date('d-F-Y');
        $title = $dateMonth.' DELIVERY MENU';
        return $title;
    }

    public function collection()
    {
        $data = DeliveryCsv::getAllDelivery();
        // echo '<pre>';print_r($data);exit;
        foreach ($data as $key => $value) {
            // echo '<pre>';print_r($value['snack_list']);
            $finalCsvData[] = [
                'name'          => $value['user_name'],
                'phone'         => $value['phone'],
                'address'       => $value['address'],
                'google_code'   => $value['google_code'],
                'emirate'       => $value['emirate'],
                'time_slot'     => $value['time_slot'],
            ];
        }
        // echo '<pre>';print_r($finalCsvData);
        // exit;
        return collect($finalCsvData);
    }

    public function headings():array
    {
        $dateMonth = date('d-F-Y');
        $title = $dateMonth.' DELIVERY MENU';
        return [
            [$title],
            [
                'NAME',
                'PHONE',
                'ADDRESS',
                'GOOGLE ADDRESS CODE',
                'EMIRATE',
                'DELIVERY TIME SLOT'
            ]
        ];
    }
}
