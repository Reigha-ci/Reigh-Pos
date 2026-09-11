<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, \App\Traits\BelongsToTenant;

    protected $fillable = ['name', 'phone_number', 'points_balance', 'last_visit'];

    protected function casts(): array
    {
        return [
            'last_visit' => 'datetime',
            'points_balance' => 'integer',
        ];
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function transactions()
    {
        return $this->pointTransactions();
    }
}

