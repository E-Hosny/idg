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

        // دمج جميع الأسعار
        $allPrices = array_merge($coloredGemstonesPrices, $otherColoredGemstonesPrices);

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
