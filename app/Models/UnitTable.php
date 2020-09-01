<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnitTable extends Model implements Presentable
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        $this->table = 'unit_tables';
        $this->timestamps = false;
        $this->with = ['units'];
        $this->fillable = ['units'];

        parent::__construct($attributes);
    }

    /**
     * @return HasMany
     */
    public function units(): HasMany
    {
        return $this->hasMany(UnitTableUnit::class, 'table_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Count' => $this->count,
            'Units' => $this->units->present(),
        ];
    }
}
