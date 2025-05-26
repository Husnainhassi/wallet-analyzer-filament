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
        'label',
        'address',
        'roi',
        'winrate',
        'status',
        'created_at',
        'updated_at',
    ];
}
