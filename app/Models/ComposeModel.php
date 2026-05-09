<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComposeModel extends Model
{
    protected $table = 'tbl_compose';

    protected $primaryKey = 'id';

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'subject',
        'message',
        'compose_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function inboxes()
    {
        return $this->hasMany(InboxModel::class, 'compose_id', 'id');
    }
}
