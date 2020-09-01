<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Food extends Model implements Presentable
{
    public const TYPES = [
        'Entree',
        'Dessert',
        'Drink',
    ];

    /**
     * @param Food    $food
     * @param Request $request
     *
     * @return static
     */
    public static function populate(Food $food, Request $request): self
    {
        $food->name_i18n_id = $request->name_i18n_id;
        $food->description_i18n_id = $request->description_i18n_id;
        $food->behaviour_id = $request->behaviour_id;
        $food->type = (string) $request->type;
        $food->icon = (string) $request->icon;
        $food->price = (int) $request->price;
        $food->save();

        return $food;
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'food';
        $this->timestamps = false;
        $this->with = [
            'nameI18n',
            'descriptionI18n',
        ];
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
     * @inheritDoc
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'NameKey'        => $this->nameI18n->key,
            'DescriptionKey' => $this->descriptionI18n->key,
            'BehaviourId'    => (int) $this->behaviour_id,
            'Icon'           => (string) $this->icon,
            'Type'           => (string) $this->type,
            'Price'          => (int) $this->price,
        ];
    }

}
