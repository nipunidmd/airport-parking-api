<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Http\Resources\BookingResource;
use Illuminate\Http\Request;
use App\Http\Requests\BookingListRequest;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateInterval;
use DatePeriod;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * display available parking slots
     */
    public function index(Request $request)
    {
        try{
            $request->validate([
                'dateFrom' => 'required|date|after:today',
                'dateTo' => 'required|date|after_or_equal:dateFrom',
            ]);

            $dateFrom = new DateTime($request->input('dateFrom'));
            $dateTo = new DateTime($request->input('dateTo'));

            $dateToIncrement = new DateTime($request->input('dateTo'));
            $dateToIncrement->modify('+1 day');

            $interval = new DateInterval('P1D');
            $dateRange = new DatePeriod($dateFrom, $interval, $dateToIncrement);

            $availabilityData = [];
            $noAvailability = false;

            foreach ($dateRange as $date) {
                $currentDate = $date->format('Y-m-d');
                $isWeekend = in_array($date->format('N'), [6, 7]);
                $isSummer = in_array($date->format('n'), [6, 7, 8]); // Check if it's June (6), July (7) or August (8)
                $isWinter = in_array($date->format('n'), [12, 1, 2]); // Check if it's December (12), January (1) or February (2)

                if ($isSummer) {
                    $priceColumn = 'summer_price';
                } elseif ($isWinter) {
                    $priceColumn = 'winter_price';
                } elseif ($isWeekend) {
                    $priceColumn = 'weekend_price';
                } else {
                    $priceColumn = 'weekday_price';
                }
                //non-booked slots list
                $nonBookedSlots = DB::table('parking_slots')
                ->select('id', $priceColumn . ' as price')
                    ->whereNotIn('id', function ($query) use ($currentDate) {
                        $query->select('parking_slot_id')
                            ->from('bookings')
                            ->where('entry_date', '<=', $currentDate)
                            ->where('exit_date', '>=', $currentDate);
                    })
                    ->get();

                // format price
                $nonBookedSlots->transform(function ($slot) {
                    if(isset($slot->price))
                    {
                        $slot->price = number_format($slot->price, 2, '.', '');
                    }
                    else{
                        $slot->weekend_price = number_format($slot->weekend_price, 2, '.', '');
                    }
                    return $slot;
                });

                $availabilityData[] = [
                    'date' => $currentDate,
                    'avilable_slots' => count($nonBookedSlots),
                    'slots' => $nonBookedSlots,
                ];
            }

            return [
                'dateFrom' => $dateFrom->format('Y-m-d'),
                'dateTo' => $dateTo->format('Y-m-d'),
                'availability' => $availabilityData,
            ];
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Generic exception handler for other types of exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }
    
    /**
     * place booking
     */
    public function store(Request $request)
    {
        try{
            
            $this->checkAuthentication();

            $validatedData = $this->validateStoreRequest($request);
    
            // user inputs
            $userEntryDate = $request->input('user_entry_date');
            $userExitDate = $request->input('user_exit_date');
            $parkingSlotId = $request->input('parking_slot_id');
    
            // Check if the slot is already booked
            $isBooked = $this->checkSlotBookingStatus($userEntryDate, $userExitDate, $parkingSlotId);
    
            if ($isBooked) {
                return response()->json(['message' => 'This slot is already booked for the selected dates.'], 400);
            }
            
            //call total calculation
            $total = $this->calculateTotalPrice($userEntryDate, $userExitDate, $parkingSlotId);
    
            // Create the booking
            $booking = Booking::create([
                'user_id' => $request->input('user_id'),
                'parking_slot_id' => $parkingSlotId,
                'entry_date' => $userEntryDate,
                'exit_date' => $userExitDate,
                'total_price' => $total
            ]);
    
            return response()->json(['message' => 'Booking successful!', 'booking' => $booking], 201);
        
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Resource not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        } catch (\Exception $e) {
            // Generic exception handler for other types of exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * total booking calculation method
     */
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
                // ->select($isWeekend ? 'weekend_price' : 'weekday_price as price')
                ->select($priceColumn . ' as price')
                ->where('id', $parkingSlotId)
                ->value($priceColumn); // gets the single value
                

            $total += $priceForDay;
        }
        return $total;
    }

    /**
     * check slot booking status
     */
    public function checkSlotBookingStatus($userEntryDate, $userExitDate, $parkingSlotId)
    {
        $isBooked = Booking::where('parking_slot_id', $parkingSlotId)
        ->where(function ($query) use ($userEntryDate, $userExitDate) {
            $query->where(function ($q) use ($userEntryDate, $userExitDate) {
                // Check if any booking starts or ends within the new booking's date range
                $q->whereBetween('entry_date', [$userEntryDate, $userExitDate])
                ->orWhereBetween('exit_date', [$userEntryDate, $userExitDate]);
            })
            ->orWhere(function ($q) use ($userEntryDate, $userExitDate) {
                // Check if any booking envelops the new booking's date range
                $q->where('entry_date', '<=', $userEntryDate)
                ->where('exit_date', '>=', $userExitDate);
            })
            ->orWhere(function ($q) use ($userEntryDate, $userExitDate) {
                // Check if the new booking's date range envelops any existing booking
                $q->where('entry_date', '>=', $userEntryDate)
                ->where('exit_date', '<=', $userExitDate);
            });
        })
        ->exists();

        return $isBooked;
    }
    
    /**
     * booking update
     */
    public function update(Request $request, $bookingId)
    {
        try{

            $this->checkAuthentication();

            $today = new DateTime(); // DateTime object with the current date and time

            // Validate the request data
            $validatedData = $this->validateUpdateRequest($request);

            $userEntryDate = $request->input('user_entry_date');
            $userExitDate = $request->input('user_exit_date');
            $parkingSlotId = $request->input('parking_slot_id');

            // Fetch the booking
            $booking = Booking::findOrFail($bookingId);

            // Check if today's date is on or after the booking's entry date
            if ($today->format('Y-m-d') >= $booking->entry_date) {
                // Disallow the update and return a response
                return response()->json(['message' => 'Cannot update booking on or after the start date (' . $booking->entry_date . ').'], 403);
            }               
            
            // Check if the slot is already booked by another user
            $isBooked = Booking::where('parking_slot_id', $request->input('parking_slot_id'))
                ->where('id', '!=', $bookingId) // Exclude the current booking
                ->where(function ($query) use ($userEntryDate, $userExitDate) {
                    $query->where(function ($q) use ($userEntryDate, $userExitDate) {
                        // Check if any booking starts or ends within the new booking's date range
                        $q->whereBetween('entry_date', [$userEntryDate, $userExitDate])
                        ->orWhereBetween('exit_date', [$userEntryDate, $userExitDate]);
                    })
                    ->orWhere(function ($q) use ($userEntryDate, $userExitDate) {
                        // Check if any booking envelops the new booking's date range
                        $q->where('entry_date', '<=', $userEntryDate)
                        ->where('exit_date', '>=', $userExitDate);
                    })
                    ->orWhere(function ($q) use ($userEntryDate, $userExitDate) {
                        // Check if the new booking's date range envelops any existing booking
                        $q->where('entry_date', '>=', $userEntryDate)
                        ->where('exit_date', '<=', $userExitDate);
                    });
                })
                ->exists();

            if ($isBooked) {
                return response()->json(['message' => 'This slot is already booked for the selected dates.'], 400);
            }

            // Calculate the total price
            $total = $this->calculateTotalPrice($request->input('user_entry_date'), $request->input('user_exit_date'), $request->input('parking_slot_id'));

            // Update the booking
            $booking->update([
                'user_id' => $request->input('user_id'),
                'parking_slot_id' => $request->input('parking_slot_id'),
                'entry_date' => $request->input('user_entry_date'),
                'exit_date' => $request->input('user_exit_date'),
                'total_price' => $total
            ]);

            return response()->json(['message' => 'Booking updated successfully!', 'booking' => $booking], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Booking not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json(['error' => 'Authentication failed'], 401);
        } catch (\Exception $e) {
            // Generic exception handler for other types of exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
        
    }

    /**
     * delete booking
     */
    public function destroy($bookingId)
    {
        try {
            $user = Auth::user();
            $today = new DateTime(); // DateTime object with the current date and time

            $booking = Booking::findOrFail($bookingId);

            // Check if today's date is on or after the booking's entry date
            if ($today->format('Y-m-d') >= $booking->entry_date) {
                // Disallow the update and return a response
                return response()->json(['message' => 'Cannot delete booking on or after the start date (' . $booking->entry_date . ').'], 403);
            }

            // If the booking is found, proceed with the deletion.
            $booking->delete();

            return response()->json(['message' => 'Booking deleted successfully.'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // If the booking is not found, return a custom error message.
            return response()->json(['message' => 'Booking not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Generic exception handler for other types of exceptions
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
     *  check user auethentication
     */
    public function checkAuthentication()
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    }

    /**
     * validate store booking request
     */
    public function validateStoreRequest($request)
    {
        $request->validate([
            'user_id' => [
                'required',
                function($attribute, $value, $fail) {
                    if ($value != Auth::id()) {
                        return $fail('The '.$attribute.' must be your user id.');
                    }
                },
            ],
            'parking_slot_id' => [
                'required',
                'exists:parking_slots,id',
                'integer',
                'between:1,10'
            ],
            'user_entry_date' => 'required|date|before_or_equal:user_exit_date|after:today',
            'user_exit_date' => 'required|date|after_or_equal:user_entry_date',
        ]);
    }

    /**
     *  validate update booking request
     */
    public function validateUpdateRequest($request)
    {
        $request->validate([
            'user_id' => [
                'required',
                function($attribute, $value, $fail) {
                    if ($value != Auth::id()) {
                        return $fail('The '.$attribute.' must be your user id.');
                    }
                },
            ],
            'parking_slot_id' => [
                'required',
                'exists:parking_slots,id',
                'integer',
                'between:1,10'
            ],
            'user_entry_date' => 'required|date|before_or_equal:user_exit_date',
            'user_exit_date' => 'required|date|after_or_equal:user_entry_date',
        ]);
    }
    

}

