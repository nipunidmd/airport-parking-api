<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function makeTestPayment(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {

            $userEntryDate = $request->user_entry_date;
            $userExitDate = $request->user_entry_date;
            $parkingSlotId = $request->parking_slot_id;

            // Create a PaymentIntent with the order amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => $this->calculateTotalPrice($userEntryDate, $userExitDate, $parkingSlotId),
                'currency' => 'gbp',
                // Verify the integration
                'metadata' => ['integration_check' => 'accept_a_payment'],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];

            return response()->json($output, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

    public function calculateTotalPrice($userEntryDate, $userExitDate, $parkingSlotId)
    {
        $dateFrom = new DateTime($userEntryDate);
        $dateTo = new DateTime($userExitDate);
        $dateTo->modify('+1 day');

        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($dateFrom, $interval, $dateTo);

        $total = 0;

        foreach ($dateRange as $date) {
            $currentDate = $date->format('Y-m-d');
            $isWeekend = in_array($date->format('N'), [6, 7]);
            $isSummer = in_array($date->format('n'), [6, 7, 8]); // Check if it's July (7) or August (8)
            $isWinter = in_array($date->format('n'), [12, 1, 2]); // Check if it's June (6), July (7) or August (8)

            if ($isSummer) {
                $priceColumn = 'summer_price';
            } elseif ($isWinter) {
                $priceColumn = 'winter_price';
            } elseif ($isWeekend) {
                $priceColumn = 'weekend_price';
            } else {
                $priceColumn = 'weekday_price';
            }

            // Get the price for the specific parking slot for this date
            $priceForDay = DB::table('parking_slots')
                ->select($priceColumn)
                ->where('id', $parkingSlotId)
                ->value($priceColumn); // This gets the single value

            $total += $priceForDay;
        }
        return $total;
    }


}
