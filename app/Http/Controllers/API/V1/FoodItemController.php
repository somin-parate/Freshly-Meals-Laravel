<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodItem;

class FoodItemController extends BaseController
{
    protected $item = '';

    public function __construct(FoodItem $item)
    {
        $this->item = $item;
    }

    public function index()
    {
        $items = $this->item::latest()->paginate(10);
        $allItems = $this->item::latest()->get()->toArray();
        return $this->sendResponse(['allItems' => $allItems, 'items'=> $items], 'Freshly Users list');
        // return $this->sendResponse($items, 'Food Item list');
    }

    public function store(Request $request)
    {
        $rules = [
            'title' =>  'required',
        ];

        $customMessages = [
            'title.required' => 'Item title is required !',
        ];

        $this->validate($request, $rules, $customMessages);

        $item = new FoodItem();
        $item->title = $request->get('title');
        $item->save();
        return $this->sendResponse($item, 'Item Created Successfully');
    }

    public function show($id)
    {
        $item = FoodItem::find($id);
        return $this->sendResponse($item, 'Item Loaded Successfully');
    }

    public function update(Request $request, $id)
    {
        $rules = [
            'title' =>  'required',
        ];

        $customMessages = [
            'title.required' => 'FoodItem title is required !',
        ];

        $this->validate($request, $rules, $customMessages);

        $item = FoodItem::findOrFail($id);
        $item->update($request->all());
        return $this->sendResponse([$item], 'FoodItem Updated Successfully');
    }

    public function destroy($id)
    {
        $item = FoodItem::findOrFail($id);
        $item->delete();
        return $this->sendResponse([$item], 'FoodItem has been Deleted');
    }

    public function getFoodItemsName()
    {
        $items = FoodItem::get();
        return $this->sendResponse($items, 'FoodItems Name list');
    }

    function csvToArray($filename = '', $delimiter = ','){
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false){
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false){
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }

        return $data;
    }

    public function importCsv(Request $request){
        $file = $request->csv;

        $customerArr = $this->csvToArray($file);
        echo '<pre>';print_r($customerArr);exit;
        // for ($i = 0; $i < count($customerArr); $i ++)
        // {
        //     User::firstOrCreate($customerArr[$i]);
        // }

        return 'Jobi done or what ever';
    }
}