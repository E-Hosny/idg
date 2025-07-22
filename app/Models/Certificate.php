<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'artifact_id',
        'generated_by',
        'certificate_number',
        'received_date',
        'report_date',
        'test_date',
        'identification',
        'shape_cut',
        'weight',
        'color',
        'dimensions',
        'transparency',
        'phenomena',
        'species',
        'variety',
        'group',
        'origin_opinion',
        'conclusion',
        'comments',
        'status',
        'test_method',
        'report_number',
        'certificate_file_path',
    ];

    protected $casts = [
        'received_date' => 'date',
        'report_date' => 'date',
        'test_date' => 'date',
        'weight' => 'decimal:3',
    ];

    /**
     * Relationships
     */
    public function artifact(): BelongsTo
    {
        return $this->belongsTo(Artifact::class);
    }

    public function generatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Scopes
     */
    public function scopeIssued($query)
    {
        return $query->where('status', 'issued');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Helper Methods
     */
    public function generateCertificateNumber()
    {
        $year = now()->year;
        $month = now()->format('m');
        $lastCertificate = self::whereYear('created_at', $year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastCertificate ? (int)substr($lastCertificate->certificate_number, -4) + 1 : 1;
        
        return "GR{$year}{$month}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function markAsIssued()
    {
        $this->update(['status' => 'issued']);
        
        // Update artifact status to certified
        $this->artifact->update(['status' => 'certified']);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'draft' => 'yellow',
            'issued' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'issued' => 'Issued',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Boot method to generate certificate number
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = $certificate->generateCertificateNumber();
            }
        });
    }
}
