<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificate;
use App\Models\TestRequest;
use Illuminate\Support\Facades\Storage;

class MigrateFilesToSpaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:migrate-to-spaces 
                            {--type=all : Type of files to migrate (all, certificates, test-requests)}
                            {--dry-run : Run without actually migrating files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from local storage to DigitalOcean Spaces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be actually migrated');
        }

        $this->info('🚀 Starting file migration to Spaces...');
        $this->newLine();

        $stats = [
            'total' => 0,
            'migrated' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        if ($type === 'all' || $type === 'certificates') {
            $this->info('📄 Migrating Certificate Files...');
            $certStats = $this->migrateCertificates($dryRun);
            $stats['total'] += $certStats['total'];
            $stats['migrated'] += $certStats['migrated'];
            $stats['skipped'] += $certStats['skipped'];
            $stats['failed'] += $certStats['failed'];
        }

        if ($type === 'all' || $type === 'test-requests') {
            $this->info('📑 Migrating Test Request Files...');
            $requestStats = $this->migrateTestRequests($dryRun);
            $stats['total'] += $requestStats['total'];
            $stats['migrated'] += $requestStats['migrated'];
            $stats['skipped'] += $requestStats['skipped'];
            $stats['failed'] += $requestStats['failed'];
        }

        $this->newLine();
        $this->info('✅ Migration Complete!');
        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            [
                ['Total Files', $stats['total']],
                ['Migrated', $stats['migrated']],
                ['Skipped (already in Spaces)', $stats['skipped']],
                ['Failed', $stats['failed']],
            ]
        );

        return Command::SUCCESS;
    }

    /**
     * Migrate certificate files
     */
    private function migrateCertificates($dryRun)
    {
        $stats = ['total' => 0, 'migrated' => 0, 'skipped' => 0, 'failed' => 0];

        $certificates = Certificate::whereNotNull('uploaded_certificate_path')->get();
        $progressBar = $this->output->createProgressBar($certificates->count());

        foreach ($certificates as $certificate) {
            $stats['total']++;
            $path = $certificate->uploaded_certificate_path;

            // Check if file exists in Spaces
            if (Storage::disk('spaces')->exists($path)) {
                $stats['skipped']++;
                $progressBar->advance();
                continue;
            }

            // Check if file exists in local storage
            if (!Storage::disk('public')->exists($path)) {
                $this->newLine();
                $this->warn("⚠️  File not found in local storage: {$path}");
                $stats['failed']++;
                $progressBar->advance();
                continue;
            }

            if (!$dryRun) {
                // Migrate the file
                $migrated = migrate_file_to_spaces($path);

                if ($migrated) {
                    $stats['migrated']++;
                } else {
                    $stats['failed']++;
                    $this->newLine();
                    $this->error("❌ Failed to migrate: {$path}");
                }
            } else {
                $stats['migrated']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        return $stats;
    }

    /**
     * Migrate test request files
     */
    private function migrateTestRequests($dryRun)
    {
        $stats = ['total' => 0, 'migrated' => 0, 'skipped' => 0, 'failed' => 0];

        $testRequests = TestRequest::whereNotNull('signed_document_path')->get();
        $progressBar = $this->output->createProgressBar($testRequests->count());

        foreach ($testRequests as $testRequest) {
            $stats['total']++;
            $path = $testRequest->signed_document_path;

            // Check if file exists in Spaces
            if (Storage::disk('spaces')->exists($path)) {
                $stats['skipped']++;
                $progressBar->advance();
                continue;
            }

            // Check if file exists in local storage
            if (!Storage::disk('public')->exists($path)) {
                $this->newLine();
                $this->warn("⚠️  File not found in local storage: {$path}");
                $stats['failed']++;
                $progressBar->advance();
                continue;
            }

            if (!$dryRun) {
                // Migrate the file
                $migrated = migrate_file_to_spaces($path);

                if ($migrated) {
                    $stats['migrated']++;
                } else {
                    $stats['failed']++;
                    $this->newLine();
                    $this->error("❌ Failed to migrate: {$path}");
                }
            } else {
                $stats['migrated']++;
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        return $stats;
    }
}


