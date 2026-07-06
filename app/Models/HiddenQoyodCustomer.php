<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class HiddenQoyodCustomer extends Model
{
    protected $fillable = [
        'qoyod_customer_id',
        'hidden_by',
        'hidden_at',
    ];

    protected $casts = [
        'hidden_at' => 'datetime',
    ];

    public function hiddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by');
    }

    public static function hiddenIds(): array
    {
        return Cache::remember('hidden_qoyod_customer_ids', 60, function () {
            return static::query()->pluck('qoyod_customer_id')->map(fn ($id) => (int) $id)->all();
        });
    }

    public static function isHidden(int|string $qoyodCustomerId): bool
    {
        return in_array((int) $qoyodCustomerId, static::hiddenIds(), true);
    }

    public static function hideMany(array $ids, ?int $userId = null): int
    {
        $count = 0;
        $now = now();

        foreach (array_unique(array_map('intval', $ids)) as $id) {
            if ($id <= 0) {
                continue;
            }

            $record = static::firstOrCreate(
                ['qoyod_customer_id' => $id],
                ['hidden_by' => $userId, 'hidden_at' => $now]
            );

            if ($record->wasRecentlyCreated) {
                $count++;
            }
        }

        static::clearCache();

        return $count;
    }

    public static function showMany(array $ids): int
    {
        $deleted = static::query()
            ->whereIn('qoyod_customer_id', array_map('intval', $ids))
            ->delete();

        static::clearCache();

        return $deleted;
    }

    public static function clearCache(): void
    {
        Cache::forget('hidden_qoyod_customer_ids');
    }
}
