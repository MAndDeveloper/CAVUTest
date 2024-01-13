<?php

namespace App\Http\Controllers;
use App\Models\Price;
use App\Models\Parking;
use Carbon\Carbon;

use Illuminate\Http\Request;

class parkingController extends Controller
{
    public function pricing(Request $request)
    {
        $input = json_decode($request->json()->all());

        return priceCalc($input->arrival, $input->leaving);
    }

    public function book(Request $request)
    {
        $input = json_decode($request->json()->all());

        $parking = new Parking;
        $parking->start = $input->arrival;
        $parking->end = $input->leaving;
        $parking->price = priceCalc($input->arrival, $input->leaving);
        $parking->registration = $input->registration;
        $parking->save();

    }

    public function cancel(Request $request)
    {
        $input = json_decode($request->json()->all());

        Parking::where('registration', $input->registration)
            ->where('start', $input->arrival)
            ->delete();

        return "Success"
    }

    public function edit(Request $request)
    {
        $input = json_decode($request->json()->all());
    }

    public function priceCalc($arrival, $leaving)
    {
        $startDate = Carbon::parse($arrival);
        $endDate = Carbon::parse($leaving);

        $priceSet = Price::where('start', '>=', $startDate)
            ->orderBy('date', 'asc')
            ->first();

        $prices = array(
            'weekdayPrice' => $priceset->costday,
            'weekendPrice' => $priceset->costend
        );
        
        $weekdayCount = 0;
        $weekendCount = 0;

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Check if the current day is a weekday (Monday to Friday)
            if ($date->isWeekday()) {
                $weekdayCount++;
            } else {
                $weekendCount++;
            }
        }

        $costs = (
            $weekdayCount * $prices['weekdayPrice']) + ($weekendCount * $prices['weekendPrice']
        );

        return $costs;
    }
}
