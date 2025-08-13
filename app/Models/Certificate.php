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
        'qr_code_token',
        'qr_code_path',
        'qr_code_generated_at',
        'uploaded_certificate_path',
        'uploaded_at',
    ];

    protected $casts = [
        'received_date' => 'date',
        'report_date' => 'date',
        'test_date' => 'date',
        'weight' => 'decimal:3',
        'qr_code_generated_at' => 'datetime',
        'uploaded_at' => 'datetime',
    ];

    protected $appends = [
        'qr_code_url',
        'public_url'
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

    public function scopeUploaded($query)
    {
        return $query->where('status', 'uploaded');
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
            'uploaded' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'draft' => 'Draft',
            'issued' => 'Issued',
            'uploaded' => 'Uploaded',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Generate QR Code for certificate
     */
    public function generateQRCode()
    {
        if (empty($this->qr_code_token)) {
            $this->qr_code_token = \Str::random(32);
        }

        $url = url('/certificate/' . $this->qr_code_token);
        
        // Create QR codes directory if it doesn't exist
        $qrDir = public_path('storage/qr-codes');
        if (!file_exists($qrDir)) {
            mkdir($qrDir, 0755, true);
        }

        $qrPath = 'qr-codes/certificate-' . $this->certificate_number . '.svg';
        $fullPath = public_path('storage/' . $qrPath);

        // Generate QR Code
        $qrCodeSvg = \QrCode::format('svg')
            ->size(200)
            ->margin(2)
            ->generate($url);

        // Save SVG to file
        file_put_contents($fullPath, $qrCodeSvg);

        $this->update([
            'qr_code_token' => $this->qr_code_token,
            'qr_code_path' => $qrPath,
            'qr_code_generated_at' => now(),
        ]);

        return $this;
    }

    /**
     * Get QR Code URL
     */
    public function getQrCodeUrlAttribute()
    {
        if ($this->qr_code_path) {
            return asset('storage/' . $this->qr_code_path);
        }
        return null;
    }

    /**
     * Get public certificate URL
     */
    public function getPublicUrlAttribute()
    {
        if ($this->qr_code_token) {
            return url('/certificate/' . $this->qr_code_token);
        }
        return null;
    }

    /**
     * Boot method to generate certificate number and QR code
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = $certificate->generateCertificateNumber();
            }
            
            // Generate QR code token
            if (empty($certificate->qr_code_token)) {
                $certificate->qr_code_token = \Str::random(32);
            }
        });

        static::created(function ($certificate) {
            // Generate QR code after certificate is created
            $certificate->generateQRCode();
        });
    }
}
