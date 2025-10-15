<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'qoyod_customer_id',
        'receiving_record_no',
        'received_date',
        'received_in',
        'delivery_date',
        'received_by',
        'status',
        'notes'
    ];

    protected $casts = [
        'received_date' => 'date',
        'delivery_date' => 'date',
    ];

    /**
     * Get the artifacts for this test request.
     */
    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }

    /**
     * Generate a unique receiving record number
     */
    public static function generateReceivingRecordNo()
    {
        $date = date('ymd');
        $lastRecord = self::where('receiving_record_no', 'like', "REC{$date}%")
                         ->orderBy('receiving_record_no', 'desc')
                         ->first();
        
        if ($lastRecord) {
            $lastNumber = intval(substr($lastRecord->receiving_record_no, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "REC{$date}{$newNumber}";
    }
}