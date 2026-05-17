<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class EmailController extends Controller
{
    public static function sendEmail(User $user, $is_forgot)
    {
        $otp = random_int(100000, 999999);

        $response = Http::withToken(config('services.repohive_email.token'))
            ->acceptJson()
            ->timeout(60)
            ->post(rtrim(config('services.repohive_email.base_url'), '/').'/email/send', [
                'to' => $user->email,
                'subject' => 'Verify your account',
                'html' => "<p>Your code is <strong>{$otp}</strong>.</p>",
                'text' => "Your code is {$otp}.",
            ]);

        if ($response->successful()) {
            $cacheKey = $is_forgot ? 'forgot_otp:' : 'otp:';
            Cache::put($cacheKey.$user->id, $otp, 300);
            Cache::put('otp_resend:'.$user->id, now()->addSeconds(60), 60);

            return null;
        }

        return 'Failed to send OTP. Please try again.';
    }
}
