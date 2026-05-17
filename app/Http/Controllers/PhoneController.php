<?php

namespace App\Http\Controllers;

use App\Models\PhoneModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class PhoneController extends Controller
{
    public function verifyPhone(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $target = Session::get('otp_target');
        $storedOTP = Session::get('otp:'.$target);
        if (! $storedOTP || (string) $request->otp !== (string) $storedOTP) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        PhoneModel::firstOrCreate(['phone_number' => $target], [
            'user_id' => Auth::id()
        ]);

        Session::forget(['otp_target', 'otp:'.$target]);

        return redirect()->route('view.profile')->with([
            'success' => 'Phone number verified successfully.',
            'phone_verification' => true,
        ]);
    }

    public function sendSms(Request $request)
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30', 'unique:tbl_phone,phone_number']
        ]);

        $phone = $validated['phone'];

        $otp = random_int(100000, 999999);

        $response = Http::withToken(config('services.repohive_sms.token'))
            ->acceptJson()
            ->timeout(30)
            ->post(
                rtrim(config('services.repohive_sms.base_url'), '/').'/messages',
                [
                    'phone' => $phone,
                    'message' => "Your verification code is {$otp}. Dont Share this to anyone!",
                ]
            );

        if ($response->successful()) {
            Session::put('otp_target', $phone);
            Session::put('otp:'.$phone, $otp);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json(),
                ]);
            }

            return back();
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS.',
                'error' => $response->json(),
            ], 500);
        }

        return back()
            ->withInput()
            ->with('sms_error', 'Failed to send OTP. Please try again.');
    }
}
