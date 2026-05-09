<?php

namespace App\Providers;

use App\Models\InboxModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.nav-sidemenu', function ($view) {
            $receiveInbox = collect();
            $sentInbox = collect();
            $drafts = collect();
            $archived = collect();
            $trashed = collect();

            if (Auth::check()) {
                $userId = Auth::id();

                $receiveInbox = InboxModel::where('user_id', $userId)
                    ->where('type', 'receiver')
                    ->where('is_draft', false)
                    ->where('is_trash', false)
                    ->where('is_archived', false)
                    ->get();

                $sentInbox = InboxModel::where('user_id', $userId)
                    ->where('type', 'sender')
                    ->where('is_archived', false)
                    ->where('is_draft', false)
                    ->where('is_trash', false)
                    ->get();

                $drafts = InboxModel::where('user_id', $userId)
                    ->where('is_draft', true)
                    ->where('is_trash', false)
                    ->get();

                $archived = InboxModel::where('user_id', $userId)
                    ->where('is_archived', true)
                    ->where('is_trash', false)
                    ->get();

                $trashed = InboxModel::where('user_id', $userId)
                    ->where('is_trash', true)
                    ->get();
            }

            $view->with(compact('receiveInbox', 'sentInbox', 'archived', 'drafts', 'trashed'));
        });
    }
}
