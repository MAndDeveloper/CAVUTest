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

        foreach (Price::all() as $price) {
            // Check if given dates are between model dates, count days, provide price
            if (Carbon::parse($input->arrival)->betweenIncluded($price->start, $price->end)) {
                $days = $input->arrival->diffInDays($input->leaving);
                $cost = $days * $price->cost;
            };
        };

        return $cost;
    }

    public function book(Request $request)
    {
        $input = json_decode($request->json()->all());
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
}
