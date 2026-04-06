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
        'notes',
        'signed_document_path',
        'lab_delivery_signed_document_path',
        'redelivery_from_lab_signed_document_path',
    ];

    protected $casts = [
        'received_date' => 'date',
        'delivery_date' => 'date',
    ];

    protected $appends = [
        'signed_document_url',
        'lab_delivery_signed_document_url',
        'redelivery_from_lab_signed_document_url',
    ];

    /**
     * Get the artifacts for this test request.
     */
    public function artifacts()
    {
        return $this->hasMany(Artifact::class);
    }

    /**
     * Get signed document URL (from Spaces or local storage)
     */
    public function getSignedDocumentUrlAttribute()
    {
        if ($this->signed_document_path) {
            return file_url($this->signed_document_path);
        }
        return null;
    }

    public function getLabDeliverySignedDocumentUrlAttribute()
    {
        if ($this->lab_delivery_signed_document_path) {
            return file_url($this->lab_delivery_signed_document_path);
        }
        return null;
    }

    public function getRedeliveryFromLabSignedDocumentUrlAttribute()
    {
        if ($this->redelivery_from_lab_signed_document_path) {
            return file_url($this->redelivery_from_lab_signed_document_path);
        }
        return null;
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