<?php

use App\Models\Artifact;
use App\Models\TestRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_request_redeliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_request_id')->constrained('test_requests')->cascadeOnDelete();
            $table->unsignedInteger('delivered_pieces_count');
            $table->unsignedInteger('remaining_pieces_count');
            $table->string('signed_document_path')->nullable();
            $table->timestamps();

            $table->index('test_request_id');
        });

        if (Schema::hasColumn('test_requests', 'redelivery_from_lab_signed_document_path')) {
            $rows = DB::table('test_requests')
                ->whereNotNull('redelivery_from_lab_signed_document_path')
                ->get();

            foreach ($rows as $tr) {
                $evaluated = Artifact::where('test_request_id', $tr->id)
                    ->whereIn('status', ['evaluated', 'certified'])
                    ->count();
                $pending = Artifact::where('test_request_id', $tr->id)
                    ->whereIn('status', ['pending', 'under_evaluation'])
                    ->count();

                DB::table('test_request_redeliveries')->insert([
                    'test_request_id' => $tr->id,
                    'delivered_pieces_count' => $evaluated,
                    'remaining_pieces_count' => $pending,
                    'signed_document_path' => $tr->redelivery_from_lab_signed_document_path,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Schema::table('test_requests', function (Blueprint $table) {
                $table->dropColumn('redelivery_from_lab_signed_document_path');
            });
        }
    }

    public function down(): void
    {
        Schema::table('test_requests', function (Blueprint $table) {
            $table->string('redelivery_from_lab_signed_document_path')->nullable()->after('lab_delivery_signed_document_path');
        });

        $redeliveries = DB::table('test_request_redeliveries')
            ->orderBy('id')
            ->get()
            ->groupBy('test_request_id');

        foreach ($redeliveries as $testRequestId => $batch) {
            $last = $batch->sortByDesc('id')->first();
            if ($last && $last->signed_document_path) {
                DB::table('test_requests')
                    ->where('id', $testRequestId)
                    ->update(['redelivery_from_lab_signed_document_path' => $last->signed_document_path]);
            }
        }

        Schema::dropIfExists('test_request_redeliveries');
    }
};
