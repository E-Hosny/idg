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
        'receiving_record_no',
        'phone',
        'email',
        'address',
        'received_date',
        'delivery_date',
        'received_in',
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
