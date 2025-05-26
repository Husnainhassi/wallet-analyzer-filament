<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wallets';

    protected $fillable = [
        'address',
        'roi',
        'win_rate',
        'status',
        'created_at',
        'updated_at',
    ];
}
