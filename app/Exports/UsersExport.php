<?php

namespace App\Exports;

use App\Models\KitchenUserCsv;
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

class UsersExport implements ShouldAutoSize, FromCollection, WithHeadings, WithStartRow, WithTitle, WithStyles
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

    public function title() : String
    {
        $dateMonth = date('d-F-Y', strtotime('+2 days'));
        $title = $dateMonth.' BREAKDOWN';
        return $title;
    }

    public function collection()
    {
        $data = KitchenUserCsv::getAllUsers();
        foreach ($data as $key => $value) {
            // echo '<pre>';print_r($value);
            $finalCsvData[] = [
                'food_item'         => $value['item_name'],
                'bl_male'           => $value['plan_data']->BL_Male,
                'bl_female'         => $value['plan_data']->BL_Female,
                'wl_male'           => $value['plan_data']->WL_Male,
                'wl_female'         => $value['plan_data']->WL_Female,
                'pcos_male'         => $value['plan_data']->PCOS_Male,
                'pcos_female'       => $value['plan_data']->PCOS_Female,
                'gf_df_male'        => $value['plan_data']->gf_df_Male,
                'gf_df_female'      => $value['plan_data']->gf_df_Female,
                'mg_male'           => $value['plan_data']->MG_Male,
                'mg_female'         => $value['plan_data']->MG_Female,
                'cp_male'           => $value['plan_data']->CP_Male,
                'cp_female'         => $value['plan_data']->CP_Female,
                'total'             => $value['total_count'],
            ];
        }
        return collect($finalCsvData);
    }

    public function headings():array
    {
        $dateMonth = date('d-F-Y', strtotime('+3 days'));
        $title = $dateMonth.' BREAKDOWN';
        return [
            [$title],
            [
                'Food Items',
                'BL_Male',
                'BL_Female',
                'WL_Male',
                'WL_Female',
                'PCOS_Male',
                'PCOS_Female',
                'GF/DF_Male',
                'GF/DF_Female',
                'MG_Male',
                'MG_Female',
                'CP_Male',
                'CP_Female',
                'Grand Total',
            ]
        ];
    }
}
