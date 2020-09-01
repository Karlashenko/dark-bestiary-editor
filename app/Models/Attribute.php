<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\AttributeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Attribute
 *
 * @package App\Models
 */
class Attribute extends Model implements Presentable
{
    /**
     * @param Attribute        $attribute
     * @param AttributeRequest $request
     *
     * @return Attribute
     * @throws \Throwable
     */
    public static function populate(Attribute $attribute, AttributeRequest $request): self
    {
        DB::transaction(function () use ($attribute, $request) {
            $attribute->index = (int) $request->index;
            $attribute->icon = (string) $request->icon;
            $attribute->is_primary = (bool) $request->is_primary;
            $attribute->is_secondary = (bool) $request->is_secondary;
            $attribute->name_i18n_id = $request->name_i18n_id;
            $attribute->description_i18n_id = $request->description_i18n_id;
            $attribute->type = \ucfirst(trim($request->type));
            $attribute->save();

            $attribute->propertyModifiers()->delete();

            foreach ((array) $request->get('property_modifiers', []) as $data) {
                $modifier = new PropertyModifier();
                $modifier->modifiable_id = $attribute->id;
                $modifier->modifiable_type = \get_class($attribute);
                $modifier->property_id = \array_get($data, 'property_id');
                $modifier->amount = (float) \array_get($data, 'amount');
                $modifier->type = (string) \array_get($data, 'type');
                $modifier->fraction = (float) \array_get($data, 'fraction');
                $modifier->fraction_property_id = \array_get($data, 'fraction_property_id');
                $modifier->fraction_attribute_id = \array_get($data, 'fraction_attribute_id');
                $modifier->max_attribute_fraction = (float) \array_get($data, 'max_attribute_fraction');
                $modifier->formula = (string) \array_get($data, 'formula');
                $modifier->save();
            }
        });

        $attribute->load($attribute->with);

        return $attribute;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'attributes';
        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n', 'propertyModifiers'];
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
     * @return MorphMany
     */
    public function propertyModifiers(): MorphMany
    {
        return $this->morphMany(PropertyModifier::class, 'modifiable')->orderBy('id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'                  => $this->id,
            'Index'               => $this->index,
            'Type'                => $this->type,
            'Icon'                => $this->icon,
            'IsPrimary'           => $this->is_primary,
            'IsSecondary'         => $this->is_secondary,
            'NameKey'             => $this->nameI18n->key,
            'DescriptionKey'      => $this->descriptionI18n->key,
            'PropertyModifiers'   => $this->propertyModifiers->present(),
        ];
    }

}
