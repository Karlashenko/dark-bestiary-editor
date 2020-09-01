<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitTableUnit extends Model implements Presentable
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'unit_table_units';
        $this->timestamps = false;
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'UnitId'       => (int) $this->unit_id,
            'Probability'  => (float) $this->probability,
            'IsUnique'     => (bool) $this->is_unique,
            'IsGuaranteed' => (bool) $this->is_guaranteed,
            'IsEnabled'    => (bool) $this->is_enabled,
        ];
    }
}
