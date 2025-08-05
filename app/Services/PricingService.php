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
        \Log::info('PricingService::calculatePrice called with:', [
            'artifactType' => $artifactType,
            'serviceType' => $serviceType,
            'weight' => $weight
        ]);

        if (!$weight || $weight <= 0) {
            \Log::warning('Invalid weight provided:', ['weight' => $weight]);
            return null;
        }

        $pricing = Pricing::findPrice($artifactType, $serviceType, $weight);
        
        if (!$pricing) {
            \Log::warning('No pricing found for:', [
                'artifactType' => $artifactType,
                'serviceType' => $serviceType,
                'weight' => $weight
            ]);
            
            // تحقق من وجود بيانات تسعير لهذا النوع
            $availablePricing = Pricing::where('artifact_type', $artifactType)->get();
            \Log::info('Available pricing for this type:', [
                'count' => $availablePricing->count(),
                'pricing' => $availablePricing->toArray()
            ]);
            
            // إذا لم توجد بيانات تسعير، جرب البحث بدون service_type
            if ($availablePricing->count() === 0) {
                \Log::info('Trying to find pricing without service type');
                $pricing = Pricing::where('artifact_type', $artifactType)
                    ->where('min_weight', '<=', $weight)
                    ->where(function($query) use ($weight) {
                        $query->where('max_weight', '>=', $weight)
                              ->orWhereNull('max_weight');
                    })
                    ->orderBy('min_weight', 'desc')
                    ->first();
                    
                if ($pricing) {
                    \Log::info('Found pricing without service type:', $pricing->toArray());
                    return $pricing->price;
                }
            }
            
            return null;
        }

        \Log::info('Pricing found:', [
            'pricing_id' => $pricing->id,
            'price' => $pricing->price,
            'min_weight' => $pricing->min_weight,
            'max_weight' => $pricing->max_weight
        ]);

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