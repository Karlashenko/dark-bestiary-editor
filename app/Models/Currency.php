<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\CurrencyRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Currency
 *
 * @package App\Models
 */
class Currency extends Model implements Presentable
{
    public const TYPES = [
        'Gold',
        'VisionFragment',
    ];

    /**
     * @param Currency        $currency
     * @param CurrencyRequest $request
     *
     * @return Currency
     */
    public static function populate(Currency $currency, CurrencyRequest $request): self
    {
        $currency->name_i18n_id = $request->name_i18n_id;
        $currency->description_i18n_id = $request->description_i18n_id;
        $currency->type = \ucfirst(trim($request->type));
        $currency->icon = (string) $request->icon;
        $currency->save();

        $currency->load($currency->with);

        return $currency;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $currencies = [])
    {
        parent::__construct($currencies);

        $this->table = 'currencies';
        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n'];
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
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'Type'           => $this->type,
            'Icon'           => $this->icon,
            'NameKey'        => $this->nameI18n->key,
            'DescriptionKey' => $this->descriptionI18n->key,
        ];
    }

}
