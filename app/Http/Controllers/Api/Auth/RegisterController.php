<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /**
     * user registration
     */
    public function register(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6|confirmed',
                'tel_no' => ['required', 'regex:/^(\+\d{1,3})?,?\s?\d{8,13}$/'],
                'street' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'postcode' => 'required|string|max:255',
                'county' => 'nullable|string|max:255',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'vehicle_reg_no' => $request ->vehicle_reg_no,
                'tel_no' => $request ->tel_no,
                'street' => $request->street,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'county' => $request->county
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json(['message' => 'User successfully registered', 
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user], 201);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    /**
     * user account update
     */
    public function update(Request $request, User $user)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'tel_no' => ['required', 'regex:/^(\+\d{1,3})?,?\s?\d{8,13}$/'],
                'street' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'postcode' => 'required|string|max:255',
                'county' => 'nullable|string|max:255',
            ]);

            $user->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'tel_no' => $request->tel_no,
                'vehicle_reg_no' => $request->vehicle_reg_no,
                'street' => $request->street,
                'city' => $request->city,
                'postcode' => $request->postcode,
                'county' => $request->county
            ]);

            return response()->json(['message' => 'User successfully updated', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
