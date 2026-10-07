<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QoyodCustomerProfile extends Model
{
    protected $fillable = [
        'qoyod_customer_id',
        'company_representative_name',
    ];

    /**
     * @return array<int, string|null> keyed by qoyod_customer_id
     */
    public static function representativeNamesByCustomerIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));

        if ($ids === []) {
            return [];
        }

        return static::query()
            ->whereIn('qoyod_customer_id', $ids)
            ->pluck('company_representative_name', 'qoyod_customer_id')
            ->all();
    }

    public static function upsertForCustomer(int $qoyodCustomerId, ?string $companyRepresentativeName): void
    {
        if ($qoyodCustomerId <= 0) {
            return;
        }

        static::updateOrCreate(
            ['qoyod_customer_id' => $qoyodCustomerId],
            ['company_representative_name' => blank($companyRepresentativeName) ? null : trim($companyRepresentativeName)]
        );
    }
}
