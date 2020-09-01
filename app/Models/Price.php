<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Price
 *
 * @package App\Models
 */
class Price extends Model implements Presentable
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'prices';
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return parent::toArray();
    }

    /**
     * @return MorphTo
     */
    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'CurrencyId' => $this->currency->id,
            'Amount'     => (int) $this->amount,
        ];
    }

}
