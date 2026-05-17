<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneModel extends Model
{
    protected $table = "tbl_phone";

    protected $fillable = [
        'user_id',
        'phone_number'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
