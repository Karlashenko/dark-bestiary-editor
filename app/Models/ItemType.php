<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ItemType extends Model implements Presentable
{
    public const EQUIPMENT_STRATEGIES = [
        'NotEquippable',
        'Unique',
        'Default',
        'TwoHandedWeapon',
        'OneHandedWeapon',
    ];

    /**
     * @param Request  $request
     * @param ItemType $type
     *
     * @return ItemType
     */
    public static function populate(ItemType $type, Request $request): self
    {
        $type->name_i18n_id = $request->name_i18n_id;
        $type->mastery_id = $request->mastery_id;
        $type->type = $request->type;
        $type->max_socket_count = (int) $request->max_socket_count;
        $type->equipment_strategy_type = (string) $request->equipment_strategy_type;
        $type->save();

        return $type;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'item_types';
        $this->with = ['nameI18n'];
        $this->timestamps = false;
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'                    => $this->id,
            'NameKey'               => $this->nameI18n->key,
            'Type'                  => $this->type,
            'MasteryId'             => (int) $this->mastery_id,
            'MaxSocketCount'        => $this->max_socket_count,
            'EquipmentStrategyType' => $this->equipment_strategy_type,
        ];
    }

}
