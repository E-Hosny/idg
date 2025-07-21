<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $table = 'pricing';

    protected $fillable = [
        'artifact_type',
        'service_type',
        'min_weight',
        'max_weight',
        'price'
    ];

    protected $casts = [
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'price' => 'decimal:2'
    ];

    /**
     * البحث عن السعر بناءً على نوع القطعة والخدمة والوزن
     */
    public static function findPrice($artifactType, $serviceType, $weight)
    {
        return self::where('artifact_type', $artifactType)
            ->where('service_type', $serviceType)
            ->where('min_weight', '<=', $weight)
            ->where(function($query) use ($weight) {
                $query->where('max_weight', '>=', $weight)
                      ->orWhereNull('max_weight');
            })
            ->orderBy('min_weight', 'desc')
            ->first();
    }

    /**
     * الحصول على جميع الأسعار لنوع قطعة معين
     */
    public static function getPricesByType($artifactType)
    {
        return self::where('artifact_type', $artifactType)
            ->orderBy('min_weight', 'asc')
            ->get();
    }
}
