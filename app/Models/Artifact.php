<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Artifact extends Model
{
    use HasTranslations;

    protected $fillable = [
        'client_id',
        'qoyod_customer_id',
        'test_request_id',
        'artifact_code',
        'type',
        'subtype',
        'service',
        'weight',
        'weight_unit',
        'price',
        'notes',
        'delivery_type',
        'specific_delivery_date',
        'expected_date',
        'title',
        'description',
        'category_id',
        'origin_country',
        'origin_city',
        'period',
        'material',
        'dimensions',
        'condition',
        'estimated_value',
        'currency',
        'images',
        'acquisition_notes',
        'acquisition_date',
        'previous_owner',
        'provenance',
        'status',
        'is_authentic',
        'assigned_to',
        'evaluation_deadline',
        'internal_notes',
        'qr_token',
        'qr_code_path',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'dimensions' => 'array',
        'images' => 'array',
        'provenance' => 'array',
        'estimated_value' => 'decimal:2',
        'acquisition_date' => 'date',
        'expected_date' => 'date',
        'specific_delivery_date' => 'date',
        'evaluation_deadline' => 'datetime',
        'is_authentic' => 'boolean',
    ];

    public $translatable = ['title', 'description'];

    /**
     * Promised piece delivery date.
     * A saved specific date wins. Otherwise count business days from the
     * receiving date, skipping Friday, so the printed date follows the piece
     * instead of the day the report is opened.
     */
    public function deliveryDateLabel($anchor = null): string
    {
        if ($this->specific_delivery_date) {
            return \Carbon\Carbon::parse($this->specific_delivery_date)->format('d/m/Y');
        }

        if ($this->expected_date) {
            return \Carbon\Carbon::parse($this->expected_date)->format('d/m/Y');
        }

        $start = $anchor
            ? \Carbon\Carbon::parse($anchor)->startOfDay()
            : ($this->created_at ? $this->created_at->copy()->startOfDay() : \Carbon\Carbon::now()->startOfDay());

        $days = match ($this->delivery_type) {
            'Regular' => 7,
            'Express Service', 'Same Day' => 0,
            '24 hours' => 1,
            '48 hours' => 2,
            '72 hours' => 3,
            default => null,
        };

        if ($days === null) {
            return '-';
        }

        $date = $start->copy();
        if ($days === 0) {
            if ($date->dayOfWeek === 5) {
                $date->addDay();
            }

            return $date->format('d/m/Y');
        }

        $added = 0;
        while ($added < $days) {
            $date->addDay();
            if ($date->dayOfWeek !== 5) {
                $added++;
            }
        }

        return $date->format('d/m/Y');
    }

    /**
     * Lab users evaluate pieces without seeing who the customer is.
     */
    public function concealCustomerForLab(): static
    {
        if (auth()->user()?->isLab()) {
            $this->unsetRelation('client');
            $this->makeHidden(['client']);
        }

        return $this;
    }

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(ArtifactEvaluation::class);
    }

    public function finalEvaluation()
    {
        return $this->hasOne(ArtifactEvaluation::class)->where('is_final', true);
    }

    public function diamondEvaluations(): HasMany
    {
        return $this->hasMany(DiamondEvaluation::class);
    }

    public function jewelleryEvaluations(): HasMany
    {
        return $this->hasMany(JewelleryEvaluation::class);
    }

    public function preciousMetalsEvaluations(): HasMany
    {
        return $this->hasMany(PreciousMetalsEvaluation::class);
    }

    public function isPreciousMetalsType(): bool
    {
        return $this->type === 'Precious & Non-Precious Metals';
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function latestCertificate()
    {
        return $this->hasOne(Certificate::class)->latest();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function testRequest()
    {
        return $this->belongsTo(TestRequest::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getTitleAttribute($value)
    {
        $titles = json_decode($value, true);
        return $titles[app()->getLocale()] ?? $titles['en'] ?? '';
    }

    public function getDescriptionAttribute($value)
    {
        $descriptions = json_decode($value, true);
        return $descriptions[app()->getLocale()] ?? $descriptions['en'] ?? '';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => __('Pending'),
            'under_evaluation' => __('Under Evaluation'),
            'evaluated' => __('Evaluated'),
            'certified' => __('Certified'),
            'rejected' => __('Rejected'),
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getConditionLabelAttribute()
    {
        $labels = [
            'excellent' => __('Excellent'),
            'very_good' => __('Very Good'),
            'good' => __('Good'),
            'fair' => __('Fair'),
            'poor' => __('Poor'),
        ];

        return $labels[$this->condition] ?? $this->condition;
    }

    // Helper methods
    public static function generateArtifactCode($type = null)
    {
        // تحديد البادئة بناءً على نوع القطعة
        $prefix = match ($type) {
            'Colored Gemstones', 'Other Colored Gemstones' => 'GR',
            'Colorless Diamonds' => 'DR',
            'Jewellery' => 'JR',
            'Precious & Non-Precious Metals' => 'MR',
            default => 'IDG-' . date('Y') . '-'
        };
        
        // إذا كان النوع من الأنواع الجديدة، استخدم الصيغة الجديدة
        if (in_array($type, ['Colored Gemstones', 'Other Colored Gemstones', 'Colorless Diamonds', 'Jewellery', 'Precious & Non-Precious Metals'])) {
            return \DB::transaction(function () use ($prefix) {
                // توليد 10 أرقام عشوائية
                $randomNumbers = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
                
                // التأكد من أن المعرف فريد
                $code = $prefix . $randomNumbers;
                $attempts = 0;
                $maxAttempts = 10;
                
                while (self::where('artifact_code', $code)->exists() && $attempts < $maxAttempts) {
                    $randomNumbers = str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
                    $code = $prefix . $randomNumbers;
                    $attempts++;
                }
                
                return $code;
            });
        }
        
        // الصيغة القديمة للأنواع الأخرى
        $year = date('Y');
        
        // Use database transaction to prevent race conditions
        return \DB::transaction(function () use ($year) {
            // Get the highest number for this year by looking at all artifact codes
            $lastArtifact = self::where('artifact_code', 'like', 'IDG-' . $year . '-%')
                ->get()
                ->map(function ($artifact) {
                    // Extract the number from codes like IDG-2025-001, IDG-2025-TEST6, etc.
                    if (preg_match('/IDG-' . date('Y') . '-(\d+)$/', $artifact->artifact_code, $matches)) {
                        return (int) $matches[1];
                    }
                    return 0;
                })
                ->max();
            
            $number = $lastArtifact ? $lastArtifact + 1 : 1;
            
            return 'IDG-' . $year . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }
}
