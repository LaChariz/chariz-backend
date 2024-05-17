<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'sometimes|string|max:20',
            'country' => 'sometimes|string|max:255',
            'avatar' => 'sometimes|url',
        ]);

        $user->fill($validatedData);

        $user->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
                    ? response()->json(['status' => __($status)], 200)
                    : response()->json(['email' => __($status)], 400);
    }

    
}
