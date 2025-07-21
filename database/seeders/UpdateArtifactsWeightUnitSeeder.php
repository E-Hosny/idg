<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Artifact;

class UpdateArtifactsWeightUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Update all artifacts that have weight but no weight_unit
        $artifacts = Artifact::whereNotNull('weight')
                            ->whereNull('weight_unit')
                            ->get();

        foreach ($artifacts as $artifact) {
            // Set default weight unit to 'ct' (carat) for existing artifacts
            $artifact->update(['weight_unit' => 'ct']);
        }

        $this->command->info("Updated {$artifacts->count()} artifacts with default weight unit 'ct'");
    }
}
