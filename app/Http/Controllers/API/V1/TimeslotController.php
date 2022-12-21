<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Timeslot;
use App\Models\City;
use Illuminate\Support\Facades\DB as FacadesDB;
use DB;

use function PHPUnit\Framework\isEmpty;

class TimeslotController extends BaseController
{

    public function slotsWithPaginate()
    {
        $timeslots = Timeslot::latest('id')->paginate(10);
        return $this->sendResponse($timeslots, 'Timeslots List');
    }

    public function addSlot(Request $request)
    {
        $slot = Timeslot::create([
            'start_time' => $request->get('start_time'),
            'end_time'   => $request->get('end_time'),
        ]);
        return $this->sendResponse($slot, 'Slot Created Successfully');
    }

    public function editSlot(Request $request, $id)
    {
        $slot = Timeslot::findOrFail($id);
        $slot->update($request->all());
        return $this->sendResponse($slot, 'Slot Updated Successfully');
    }

    public function deleteSlot($id)
    {
        $slot = Timeslot::findOrFail($id);
        $slot->delete();
        return $this->sendResponse($slot, 'Slot Deleted Successfully');
    }

    public function loadTimeSlots()
    {
        $timeslots = Timeslot::all();
        $timeslots->each(function ($item) {
            $item->slot_time = $this->covertTime($item->start_time, $item->end_time);
        });
        return $this->sendResponse($timeslots, 'Timeslots List');
    }

    public function index()
    {
        $cities = City::with('timeslots')->paginate(10);
        $allCities = City::with('timeslots')->get()->toArray();
        return $this->sendResponse(['allCities' => $allCities, 'cities'=> $cities], 'Cities List');
        // return $this->sendResponse($cities, 'Cities List');
    }

    public function store(Request $request)
    {
        $rules = [
            'city'  =>  'required',
            'code'  =>  'required',
        ];

        $customMessages = [
            'city.required' => 'Enter City Name!',
            'code.required' => 'Enter City Code!',
        ];
        $this->validate($request, $rules, $customMessages);

        $city = City::create([
            'city' => $request->get('city'),
            'code' => $request->get('code'),
        ]);
        $city->timeslots()->sync($request->timeslots_options);

        return $this->sendResponse($city, 'City Created Successfully');
    }

    public function update(Request $request)
    {
        $rules = [
            'city'  =>  'required',
            'code'  =>  'required',
        ];

        $customMessages = [
            'city.required' => 'Enter City Name!',
            'code.required' => 'Enter City Code!',
        ];
        $this->validate($request, $rules, $customMessages);

        $city = City::findOrFail($request->id);
        $city->timeslots()->sync($request->timeslots_options);
        $city->update($request->all());
        return $this->sendResponse($city, 'City Updated Successfully');
    }

    public function destroy($id)
    {
        $city = City::findOrFail($id);

        $timeslotsIds = FacadesDB::table('city_timeslot')->where([
            'city_id'    => $id,
        ])->pluck('timeslot_id');

        $city->timeslots()->detach($timeslotsIds);
        $city->delete();

        return $this->sendResponse($city, 'City has been Deleted');
    }
}
