<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Mastery extends Model implements Presentable
{
    /**
     * @param Mastery $mastery
     * @param Request $request
     *
     * @return Mastery
     */
    public static function populate(Mastery $mastery, Request $request): self
    {
        $mastery->type = (string) $request->type;
        $mastery->name_i18n_id = $request->name_i18n_id;
        $mastery->description_i18n_id = $request->description_i18n_id;
        $mastery->item_modifier_id = $request->item_modifier_id;
        $mastery->item_type_id = $request->item_type_id;
        $mastery->damage_type = (string) $request->damage_type;
        $mastery->save();

        $mastery->load($mastery->with);

        return $mastery;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'masteries';
        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n'];
    }

    /**
     * @return BelongsTo
     */
    public function itemType(): BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'item_type_id')
                    ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')
                    ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function descriptionI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')
                    ->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'Type'           => $this->type,
            'NameKey'        => $this->nameI18n->key,
            'DescriptionKey' => $this->descriptionI18n->key,
            'ModifierId'     => (int) $this->item_modifier_id,
            'DamageType'     => (string) $this->damage_type ?: 'None',
            'ItemType'       => (string) $this->itemType->type ?: 'None',
        ];
    }

}
