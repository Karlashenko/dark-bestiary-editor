<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class Relic
 *
 * @package App\Models
 */
class Relic extends Model implements Presentable
{
    /**
     * @param Relic   $relic
     * @param Request $request
     *
     * @return Relic
     * @throws \Throwable
     */
    public static function populate(Relic $relic, Request $request): self
    {
        DB::transaction(function () use ($relic, $request) {
            $relic->name_i18n_id = $request->name_i18n_id;
            $relic->description_i18n_id = $request->description_i18n_id;
            $relic->lore_i18n_id = $request->lore_i18n_id;
            $relic->behaviour_id = $request->behaviour_id;
            $relic->rarity_id = $request->rarity_id;
            $relic->icon = (string) $request->icon;
            $relic->save();
        });

        $relic->load($relic->with);

        return $relic;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'relics';
        $this->timestamps = false;
        $this->with = [
            'nameI18n',
            'descriptionI18n',
        ];
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function descriptionI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function loreI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'lore_i18n_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'NameKey'        => $this->nameI18n->key,
            'DescriptionKey' => $this->descriptionI18n->key,
            'LoreKey'        => $this->loreI18n->key,
            'BehaviourId'    => (int) $this->behaviour_id,
            'RarityId'       => (int) $this->rarity_id,
            'Icon'           => $this->icon,
        ];
    }
}
