<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class InvoiceNumber extends Model
{
    protected $fillable = [
        'type',
        'current_number',
        'prefix'
    ];

    /**
     * Generate the next invoice number in the format INVXXXX
     */
    public static function generateNextInvoiceNumber()
    {
        return DB::transaction(function () {
            $record = self::lockForUpdate()->where('type', 'invoice')->first();
            
            if (!$record) {
                // Create the record if it doesn't exist
                $record = self::create([
                    'type' => 'invoice',
                    'current_number' => 0,
                    'prefix' => 'INV'
                ]);
            }
            
            // Increment the number
            $record->increment('current_number');
            
            // Format with 4 digits padding
            $invoiceNumber = $record->prefix . str_pad($record->current_number, 4, '0', STR_PAD_LEFT);
            
            return $invoiceNumber;
        });
    }

    /**
     * Get the current invoice number without incrementing
     */
    public static function getCurrentInvoiceNumber()
    {
        $record = self::where('type', 'invoice')->first();
        
        if (!$record) {
            return 'INV0000';
        }
        
        return $record->prefix . str_pad($record->current_number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Reset the invoice counter (use with caution)
     */
    public static function resetInvoiceCounter($startFrom = 0)
    {
        return self::where('type', 'invoice')->update(['current_number' => $startFrom]);
    }
}
