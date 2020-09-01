<?php

declare(strict_types=1);

namespace App\Models;

use App\Http\Requests\PropertyRequest;
use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Property
 *
 * @package App\Models
 */
class Property extends Model implements Presentable
{
    /**
     * @param Property        $property
     * @param PropertyRequest $request
     *
     * @return Property
     */
    public static function populate(Property $property, PropertyRequest $request): self
    {
        $property->index = (int) $request->index;
        $property->abbreviation = (string) $request->abbreviation;
        $property->type = (string) $request->type;
        $property->name_i18n_id = (int) $request->name_i18n_id;
        $property->is_unscalable = (bool) $request->is_unscalable;
        $property->min = (float) $request->min;
        $property->max = (float) $request->max;
        $property->save();
        $property->load($property->with);

        return $property;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $properties = [])
    {
        parent::__construct($properties);

        $this->table = 'properties';
        $this->with = ['nameI18n'];
        $this->timestamps = false;
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
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'           => $this->id,
            'Index'        => $this->index,
            'Type'         => $this->type,
            'NameKey'      => $this->nameI18n->key,
            'IsUnscalable' => (bool) $this->is_unscalable,
            'Min'          => (float) $this->min,
            'Max'          => (float) $this->max,
        ];
    }

}
