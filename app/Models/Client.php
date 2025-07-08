<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'company_name',
        'customer_code',
        'phone',
        'email',
        'address',
        'received_date',
        'delivery_date',
        'national_id',
        'nationality',
        'notes',
        'created_by',
    ];

    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }
}
