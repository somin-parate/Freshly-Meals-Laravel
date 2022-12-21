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

class ParcelExport implements ShouldAutoSize, FromCollection, WithHeadings, WithStartRow, WithTitle, WithStyles, WithColumnWidths
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
        $title = $dateMonth.' CLIENTS MENU';
        return $title;
    }

    public function collection()
    {
        $data = DeliveryCsv::getAllParcel();
        // echo '<pre>';print_r($data);exit;
        foreach ($data as $key => $value) {
            // echo '<pre>';print_r($value['snack_list']);
            if($value['meal_list']){
                if(array_key_exists("0",$value['meal_list'])){
                    $meal_1 = $value['meal_list'][0];
                }else{
                    $meal_1 = '0';
                }
                if(array_key_exists("1",$value['meal_list'])){
                    $meal_2 = $value['meal_list'][1];
                }else{
                    $meal_2 = '0';
                }
                if(array_key_exists("2",$value['meal_list'])){
                    $meal_3 = $value['meal_list'][2];
                }else{
                    $meal_3 = '0';
                }
            }
            if(!empty($value['snack_list'])){
                // echo 'test';exit;
                if(array_key_exists("0",$value['snack_list'])){
                    $snack_1 = $value['snack_list'][0];
                }else{
                    $snack_1 = '0';
                }
                if(array_key_exists("1",$value['snack_list'])){
                    $snack_2 = $value['snack_list'][1];
                }else{
                    $snack_2 = '0';
                }
            }
            $finalCsvData[] = [
                'plan_name'         => $value['plan_name'],
                'name'              => $value['user_name'],
                'address'           => $value['address'],
                'no_of_meals'       => $value['no_of_meals'],
                'cutlery'           => $value['cutlery'],
                'meal_1'            => $meal_1,
                'meal_2'            => $meal_2,
                'meal_3'            => $meal_3,
                'snack_1'           => $snack_1,
                'snack_2'           => $snack_2,
                'notes'             => ''
            ];
        }
        // echo '<pre>';print_r($finalCsvData);
        // exit;
        return collect($finalCsvData);
    }

    public function headings():array
    {
        $dateMonth = date('d-F-Y');
        $title = $dateMonth.' CLIENTS MENU';
        return [
            [$title],
            [
                'MP',
                'NAME',
                'DELIVERY ADDRESS',
                'NO OF MEALS & SNACKS',
                'Would you require Cutlery?',
                'MEAL 1',
                'MEAL 2',
                'MEAL 3',
                'SNACK 1',
                'SNACK 2',
                'NOTES'
            ]
        ];
    }
}