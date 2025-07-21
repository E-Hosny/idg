<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pricing;

class PricingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // حذف البيانات الموجودة أولاً
        Pricing::truncate();

        // بيانات أسعار Colored Gemstones
        $coloredGemstonesPrices = [
            // REGULAR - ID Report
            ['Colored Gemstones', 'Regular - ID Report', 1.00, 1.99, 390],
            ['Colored Gemstones', 'Regular - ID Report', 2.00, 4.99, 455],
            ['Colored Gemstones', 'Regular - ID Report', 5.00, 9.99, 530],
            ['Colored Gemstones', 'Regular - ID Report', 10.00, 14.99, 610],
            ['Colored Gemstones', 'Regular - ID Report', 15.00, 19.99, 700],
            ['Colored Gemstones', 'Regular - ID Report', 20.00, 49.99, 1400],
            ['Colored Gemstones', 'Regular - ID Report', 50.00, null, 2100],
            
            // REGULAR - ID + Origin
            ['Colored Gemstones', 'Regular - ID + Origin', 1.00, 1.99, 840],
            ['Colored Gemstones', 'Regular - ID + Origin', 2.00, 4.99, 960],
            ['Colored Gemstones', 'Regular - ID + Origin', 5.00, 9.99, 1120],
            ['Colored Gemstones', 'Regular - ID + Origin', 10.00, 14.99, 1250],
            ['Colored Gemstones', 'Regular - ID + Origin', 15.00, 19.99, 1400],
            ['Colored Gemstones', 'Regular - ID + Origin', 20.00, 49.99, 2800],
            ['Colored Gemstones', 'Regular - ID + Origin', 50.00, null, 4200],
            
            // Mini Card Report - ID Report (جميع الأوزان بـ 150)
            ['Colored Gemstones', 'Mini Card Report - ID Report', 1.00, 1.99, 150],
            ['Colored Gemstones', 'Mini Card Report - ID Report', 2.00, 4.99, 150],
            ['Colored Gemstones', 'Mini Card Report - ID Report', 5.00, 9.99, 150],
            ['Colored Gemstones', 'Mini Card Report - ID Report', 10.00, 14.99, 150],
            ['Colored Gemstones', 'Mini Card Report - ID Report', 15.00, 19.99, 150],
            ['Colored Gemstones', 'Mini Card Report - ID Report', 20.00, 49.99, 150],
            ['Colored Gemstones', 'Mini Card Report - ID Report', 50.00, null, 150],
            
            // Mini Card Report - ID + Origin (جميع الأوزان بـ 300)
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 1.00, 1.99, 300],
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 2.00, 4.99, 300],
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 5.00, 9.99, 300],
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 10.00, 14.99, 300],
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 15.00, 19.99, 300],
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 20.00, 49.99, 300],
            ['Colored Gemstones', 'Mini Card Report - ID + Origin', 50.00, null, 300],
        ];

        // بيانات أسعار Other Colored Gemstones
        $otherColoredGemstonesPrices = [
            // REGULAR - ID Report فقط (لا يوجد ID + Origin)
            ['Other Colored Gemstones', 'Regular - ID Report', 1.00, 19.99, 300],
            ['Other Colored Gemstones', 'Regular - ID Report', 20.00, 49.99, 800],
            ['Other Colored Gemstones', 'Regular - ID Report', 50.00, null, 1000],
            
            // Mini Card Report - ID Report فقط (جميع الأوزان بـ 150)
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 1.00, 1.99, 150],
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 2.00, 4.99, 150],
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 5.00, 9.99, 150],
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 10.00, 14.99, 150],
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 15.00, 19.99, 150],
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 20.00, 49.99, 150],
            ['Other Colored Gemstones', 'Mini Card Report - ID Report', 50.00, null, 150],
        ];

        // بيانات أسعار Colorless Diamonds
        $colorlessDiamondsPrices = [
            // REGULAR - Diamond Grading Report
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 0.15, 0.22, 300],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 0.23, 0.46, 330],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 0.47, 0.69, 350],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 0.70, 0.99, 400],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 1.00, 1.49, 460],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 1.50, 1.99, 635],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 2.00, 2.99, 935],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 3.00, 3.99, 1230],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 4.00, 4.99, 1740],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 5.00, 5.99, 2050],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 6.00, 7.99, 2450],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 8.00, 9.99, 3380],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 10.00, 11.99, 3955],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 12.00, 14.99, 4850],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 15.00, 19.99, 5750],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 20.00, 24.99, 6700],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 25.00, 29.99, 7750],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 30.00, 39.99, 9310],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 40.00, 49.99, 10800],
            ['Colorless Diamonds', 'Regular - Diamond Grading Report', 50.00, null, null], // On request
            
            // Mini Card Report - Mini Report (متاح فقط حتى 1.49 ct)
            ['Colorless Diamonds', 'Mini Card Report - Mini Report', 0.15, 0.22, 220],
            ['Colorless Diamonds', 'Mini Card Report - Mini Report', 0.23, 0.46, 240],
            ['Colorless Diamonds', 'Mini Card Report - Mini Report', 0.47, 0.69, 280],
            ['Colorless Diamonds', 'Mini Card Report - Mini Report', 0.70, 0.99, 320],
            ['Colorless Diamonds', 'Mini Card Report - Mini Report', 1.00, 1.49, 435],
            // لا يوجد تسعير للأوزان 1.50 ct وما فوق (N/A)
        ];

        // دمج جميع الأسعار
        $allPrices = array_merge($coloredGemstonesPrices, $otherColoredGemstonesPrices, $colorlessDiamondsPrices);

        // إدخال البيانات
        foreach ($allPrices as $priceData) {
            Pricing::create([
                'artifact_type' => $priceData[0],
                'service_type' => $priceData[1],
                'min_weight' => $priceData[2],
                'max_weight' => $priceData[3],
                'price' => $priceData[4],
            ]);
        }

        $this->command->info('Pricing data updated successfully!');
        $this->command->info('Total pricing records: ' . count($allPrices));
    }
}
