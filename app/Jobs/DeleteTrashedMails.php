<?php

namespace App\Jobs;

use App\Models\InboxModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteTrashedMails implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        InboxModel::where('is_trash', true)->where('trashed_at', '<=', now()->subMonth())->delete();
    }
}
