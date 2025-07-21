<?php

namespace App\Services;

use App\Models\Pricing;

class PricingService
{
    /**
     * حساب السعر بناءً على نوع القطعة والخدمة والوزن
     */
    public static function calculatePrice($artifactType, $serviceType, $weight)
    {
        if (!$weight || $weight <= 0) {
            return null;
        }

        $pricing = Pricing::findPrice($artifactType, $serviceType, $weight);
        
        if (!$pricing) {
            return null;
        }

        // إذا كان السعر null، فهذا يعني "On request"
        if ($pricing->price === null) {
            return 'On request';
        }

        return $pricing->price;
    }

    /**
     * الحصول على جميع الأسعار المتاحة لنوع قطعة معين
     */
    public static function getPricingTable($artifactType)
    {
        return Pricing::getPricesByType($artifactType);
    }

    /**
     * التحقق من وجود تسعير لنوع قطعة معين
     */
    public static function hasPricing($artifactType)
    {
        return Pricing::where('artifact_type', $artifactType)->exists();
    }

    /**
     * الحصول على نطاق الوزن المناسب للسعر
     */
    public static function getWeightRange($artifactType, $serviceType, $weight)
    {
        $pricing = Pricing::findPrice($artifactType, $serviceType, $weight);
        
        if (!$pricing) {
            return null;
        }

        $range = $pricing->min_weight . ' - ';
        if ($pricing->max_weight) {
            $range .= $pricing->max_weight;
        } else {
            $range .= '∞';
        }
        
        // تحديد الوحدة بناءً على نوع القطعة
        $unit = 'ct';
        if ($artifactType === 'Jewellery') {
            $unit = 'gm';
        }
        
        return $range . ' ' . $unit;
    }
} 