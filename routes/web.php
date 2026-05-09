<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('pages.auth.sign-in');
    })->name('view.sign-in');
    Route::get('/sign-up', function () {
        return view('pages.auth.sign-up');
    })->name('view.sign-up');

    Route::post('/sign-in', [AuthController::class, 'signIn'])->name('auth.sign-in');
    Route::post('/sign-up', [AuthController::class, 'signUp'])->name('auth.sign-up');

    Route::get('/google/sign-in', [SocialiteController::class, 'googleRedirect'])->name('auth.google-redirect');
    Route::get('/google/callback', [SocialiteController::class, 'googleCallback'])->name('auth.google-callback');

    Route::prefix('verify')->group(function () {
        Route::get('/', function () {
            $userID = session('verification_user_id');
            if (! $userID) {
                return redirect()->route('view.sign-in');
            }

            $resend = Cache::get('otp_resend:'.$userID);
            $ttl = $resend ? (int) now()->diffInSeconds($resend) : 0;
            $ttl = max($ttl, 0);

            return view('pages.verification.index', compact('ttl'));
        })->name('view.verify');
        Route::post('/verify', [AuthController::class, 'verifyOTP'])->name('verify.otp');
        Route::get('/forget', function () {
            $userID = session()->get('verification_user_id');
            session()->forget('verification_user_id');
            Cache::forget('otp:'.$userID);
            Cache::forget('otp_resend:'.$userID);

            return redirect()->route('view.sign-up');
        })->name('verify.forget');

        Route::post('/resend-otp', [AuthController::class, 'resendOTP'])->name('verify.resend');
    });

});

Route::middleware('auth')->group(function () {
    Route::prefix('mailbox')->group(function () {
        Route::get('/', fn () => app(InboxController::class)->inbox('primary'))->name('view.primary');
        Route::get('/sent', fn () => app(InboxController::class)->inbox('sent'))->name('view.sent');
        Route::get('/drafts', fn () => app(InboxController::class)->inbox('drafts'))->name('view.drafts');
        Route::get('/important', fn () => app(InboxController::class)->inbox('important'))->name('view.important');
        Route::get('/archived', fn () => app(InboxController::class)->inbox('archived'))->name('view.archived');
        Route::get('/trash', fn () => app(InboxController::class)->inbox('trash'))->name('view.trash');
        Route::get('/search', [InboxController::class, 'search'])->name('mail.search');
        Route::get('/{section}/{inboxID}', [InboxController::class, 'show'])->name('view.mail');

        Route::post('/update', [InboxController::class, 'update'])->name('mail.update');
        Route::post('/bulk-update', [InboxController::class, 'bulkUpdate'])->name('mail.bulk-update');
        Route::post('/sent', [InboxController::class, 'store'])->name('mail.sent');
        Route::post('/drafts', [InboxController::class, 'saveDraft'])->name('mail.draft');
        Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

        Route::post('/chatbot/message', [ChatbotController::class, 'message'])->name('chatbot.message');
        Route::post('/chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
    });
});
