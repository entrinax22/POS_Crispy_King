<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordOtp;
use Illuminate\Http\Request;
use App\Mail\PasswordOtpMail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordOtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found.'], 404);
        }

        $otp = rand(100000, 999999);

        PasswordOtp::create([
            'user_id' => $user->id,
            'otp' => $otp,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::to($user->email)->send(new PasswordOtpMail($otp));

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    // Verify OTP and reset password
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Invalid email.'], 404);
        }

        $otpRecord = PasswordOtp::where('user_id', $user->id)
            ->where('otp', $request->otp)
            ->where('used', false)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        // Mark OTP as used
        $otpRecord->update(['used' => true]);

        // Update password
        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['message' => 'Password successfully updated.']);
    }
}
