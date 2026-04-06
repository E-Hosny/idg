<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TestRequestRedelivery extends Model
{
    protected static function booted(): void
    {
        static::deleting(function (TestRequestRedelivery $redelivery) {
            if ($redelivery->signed_document_path) {
                delete_file_anywhere($redelivery->signed_document_path);
            }
        });
    }

    protected $fillable = [
        'test_request_id',
        'delivered_pieces_count',
        'remaining_pieces_count',
        'signed_document_path',
    ];

    protected $casts = [
        'delivered_pieces_count' => 'integer',
        'remaining_pieces_count' => 'integer',
    ];

    protected $appends = ['signed_document_url'];

    public function testRequest(): BelongsTo
    {
        return $this->belongsTo(TestRequest::class);
    }

    public function getSignedDocumentUrlAttribute(): ?string
    {
        if ($this->signed_document_path) {
            return file_url($this->signed_document_path);
        }

        return null;
    }
}
