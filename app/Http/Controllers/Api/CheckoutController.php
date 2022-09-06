<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bookable;
use App\Models\Booking;
use App\Models\Address;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'bookings' => 'required|array|min:1',
            'bookings.*.bookable_id' => 'required|exists:bookables,id',
            'bookings.*.from' => 'required|date|after_or_equal:today',
            'bookings.*.to' => 'required|date|after_or_equal:bookings.*.from',
            'customer.first_names' => 'required|min:1',
            'customer.last_name' => 'required|min:1',
            'customer.street' => 'required|min:1',
            'customer.city' => 'required|min:1',
            'customer.country' => 'required|min:1',
            'customer.email' => 'required|email',
            'customer.state' => 'required|min:1',
            'customer.zip' => 'required|min:1'
        ]);

        $data = array_merge($data, $request->validate([
            'bookings.*' => ['required', function($attribute, $value, $fail){
                $bookable = Bookable::findOrFail($value['bookable_id']);

                if(!$bookable->availableFor($value['from'], $value['to'])){
                    $fail('Booking Not Available');
                }
            }]
        ]));

        $bookingsData = $data['bookings'];
        $addressData = $data['customer'];

        $bookings = collect($bookingsData)->map(function ($bookingData) use ($addressData){

            $bookable = Bookable::findOrFail($bookingData['bookable_id']);

            $booking = new Booking();
            $booking->from = $bookingData['from'];
            $booking->to = $bookingData['to'];
            $booking->price = $bookable->priceFor($booking->from, $booking->to)['total'];
            $booking->bookable()->associate($bookable);

            $booking->address()->associate(Address::create($addressData));

            $booking->save();

            return $booking;

        });

        return $bookings;
    }
}
