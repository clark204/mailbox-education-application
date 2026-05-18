<?php

namespace App\Services;

use App\Mail\VerifyMail;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class EmailService
{
    public static function sendEmail(User $user, bool $is_forgot)
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
        } else {
            return self::emailVerification($user, $is_forgot);
        }

        return 'Failed to send OTP. Please try again.';
    }

    public static function emailVerification(User $user, $is_forgot)
    {
        $lock = Cache::lock('otp_lock:'.$user->id, 60);

        if (! $lock->get()) {
            return 'please wait 60 seconds before requesting another OTP.';
        }

        $otp = random_int(100000, 999999);
        ! $is_forgot ? Cache::put('otp:'.$user->id, $otp, 300) : Cache::put('forgot_otp:'.$user->id, $otp, 300);
        Mail::to($user->email)->queue(new VerifyMail($user->first_name, $otp));
        Cache::put('otp_resend:'.$user->id, now()->addSeconds(60), 60);

        return null;
    }
}
