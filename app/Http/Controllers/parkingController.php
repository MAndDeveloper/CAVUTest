<?php

namespace App\Http\Controllers;
use App\Models\Price;
use App\Models\Parking;
use Carbon\Carbon;

use Illuminate\Http\Request;

class parkingController extends Controller
{
    public function price(Request $request)
    {
        $input = json_decode($request->json()->all());

        // Simple run through price calculation function, return price
        return priceCalc($input->arrival, $input->leaving);
    }

    public function book(Request $request)
    {
        $input = json_decode($request->json()->all());
        $startDate = Carbon::parse($arrival);
        $endDate = Carbon::parse($leaving);

        if (spaceCounter($startDate, $endDate) >= 10) {
            return "Bookings full";
        } else {
            // Set fillables in model, use model to save to appropriate table
            $parking = new Parking;
            $parking->start = $input->arrival;
            $parking->end = $input->leaving;
            $parking->price = priceCalc($input->arrival, $input->leaving);
            $parking->registration = $input->registration;
            $parking->save();

            return "Success";
        }
    }

    public function cancel(Request $request)
    {
        $input = json_decode($request->json()->all());

        // Find and remove based on car reg number and start date of booking
        Parking::where('registration', $input->registration)
            ->where('start', $input->arrival)
            ->delete();

        return "Success"
    }

    public function edit(Request $request)
    {
        // Request to include new and old details allowing broader changes (reg or date)
        $input = json_decode($request->json()->all());

        // Calculate new parking cost.
        $newPrice = priceCalc($input->arrival, $input->leaving);

        // Use Eloquent Update to ammend database record.
        Parking::where('registration', $input->registration)
            ->where('start', $input->oldStart)
            ->update([
                'price' => $newPrice,
                'registration' => $newReg,
                'start' => $input->arrival,
                'end' => $input->leaving
            ]);
    }

    public function priceCalc($arrival, $leaving)
    {
        // Carbonize dates for standardization.
        $startDate = Carbon::parse($arrival);
        $endDate = Carbon::parse($leaving);

        // Find prices from Price model of date set with start date nearest given parking start date
        $priceSet = Price::where('start', '>=', $startDate)
            ->orderBy('date', 'asc')
            ->first();
        
        // Create 0 Intergers for counting of days parked
        $weekdayCount = 0;
        $weekendCount = 0;

        // For loop through given dates until end date is reached
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            // Check if the current day is a weekday (Monday to Friday) and count
            if ($date->isWeekday()) {
                $weekdayCount++;
            } else {
                $weekendCount++;
            }
        }

        // Collate count of days with prices pulled from model and return
        $costs = ($weekdayCount * $priceset->costday) + ($weekendCount * $priceset->costend);

        return $costs;
    }

    public function spaceCheck(Request $request)
    {
        $input = json_decode($request->json()->all());
        $startDate = Carbon::parse($arrival);
        $endDate = Carbon::parse($leaving);

        // Check parking table to ensure a space is availible
        // so as not to go over the 10 space maximum during dates given

        $count = spaceCounter($startDate, $endDate);

        if ($count < 10) {
            return "There are " . $count . " spaces";
        } else {
            return false
        }
    }

    public function spaceCounter($startDate, $endDate)
    {
        $number = Parking::where(function ($query) use ($startDate, $endDate) {
            // Check for overlap when given start date is within the range
            $query->where('start_date', '<=', $startDate)
                    ->where('end_date', '>=', $startDate);
        })->orWhere(function ($query) use ($startDate, $endDate) {
            // Check for overlap when given end date is within the range
            $query->where('start_date', '<=', $endDate)
                    ->where('end_date', '>=', $endDate);
        })->orWhere(function ($query) use ($startDate, $endDate) {
            // Check for overlap when given range is entirely within the range
            $query->where('start_date', '>=', $startDate)
                    ->where('end_date', '<=', $endDate);
        })->count();

        return $number;
    }
}
