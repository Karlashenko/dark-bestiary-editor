<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnitGroup extends Model implements Presentable
{
    /**
     * @param UnitGroup $unitGroup
     * @param Request   $request
     *
     * @return UnitGroup
     * @throws \Throwable
     */
    public static function populate(UnitGroup $unitGroup, Request $request): self
    {
        DB::transaction(function () use ($unitGroup, $request) {
            $unitGroup->label = (string) $request->label;
            $unitGroup->save();

            $unitGroup->units()->sync([]);

            foreach ((array) $request->get('units', []) as $data) {
                $unitGroup->units()->attach((int) array_get($data, 'id'));
            }
        });

        $unitGroup->load($unitGroup->with);

        return $unitGroup;
    }

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->table = 'unit_groups';
        $this->with = ['units'];
        $this->timestamps = false;
    }

    /**
     * @return BelongsToMany
     */
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'unit_groups_units')
                    ->withPivot('id')
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'    => $this->id,
            'Units' => $this->units->pluck('id')->toArray(),
        ];
    }

}
