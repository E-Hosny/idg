<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'national_id',
        'phone',
        'email',
        'nationality',
        'address',
        'notes',
    ];

    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }
}
