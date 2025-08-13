<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\Artifact;

class UpdateQRCodeUrlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Updating QR codes for uploaded certificates...');
        
        // Get all uploaded certificates
        $uploadedCertificates = Certificate::where('status', 'uploaded')
            ->whereNotNull('uploaded_certificate_path')
            ->get();
        
        $updatedCount = 0;
        
        foreach ($uploadedCertificates as $certificate) {
            try {
                // Regenerate QR code with new URL
                $certificate->generateQRCode();
                $updatedCount++;
                
                $this->command->info("Updated QR code for certificate: {$certificate->certificate_number}");
                
            } catch (\Exception $e) {
                $this->command->error("Failed to update QR code for certificate {$certificate->certificate_number}: " . $e->getMessage());
            }
        }
        
        $this->command->info("Successfully updated {$updatedCount} QR codes!");
    }
} 