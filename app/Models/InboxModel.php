<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;

class InboxModel extends Model
{
    protected $table = 'tbl_inbox';

    protected $primaryKey = 'id';

    protected $fillable = [
        'compose_id',
        'user_id',
        'type',
        'is_read',
        'is_important',
        'is_archived',
        'inbox_id',
        'is_trash',
        'is_draft',
    ];

    #[Override]
    protected static function booted()
    {
        static::creating(function ($model) {
            $model->inbox_id = $model->generateInboxID();
        });
    }

    private function generateInboxID()
    {
        do {
            $code = Str::random(16);
        } while (self::where('inbox_id', $code)->exists());

        return $code;
    }

    public function compose(){
        return $this->belongsTo(ComposeModel::class, 'compose_id', 'id');
    }
}
