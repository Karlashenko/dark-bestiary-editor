<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\TalentRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Talent
 *
 * @package App\Models
 */
class Talent extends Model implements Presentable
{
    /**
     * @param Talent        $talent
     * @param TalentRequest $request
     *
     * @return Talent
     */
    public static function populate(Talent $talent, TalentRequest $request): self
    {
        $talent->name_i18n_id = $request->name_i18n_id;
        $talent->description_i18n_id = $request->description_i18n_id;
        $talent->category_id = $request->category_id;
        $talent->behaviour_id = $request->behaviour_id;
        $talent->tier = (int) $request->tier;
        $talent->index = (int) $request->index;
        $talent->icon = (string) $request->icon;
        $talent->save();

        $talent->load($talent->with);

        return $talent;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $talents = [])
    {
        parent::__construct($talents);

        $this->table = 'talents';
        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n', 'category'];
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
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TalentCategory::class, 'category_id')->withDefault();
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'NameKey'        => $this->nameI18n->key,
            'DescriptionKey' => $this->descriptionI18n->key,
            'BehaviourId'    => (int) $this->behaviour_id,
            'CategoryId'     => (int) $this->category_id,
            'Tier'           => (int) $this->tier,
            'Index'          => (int) $this->index,
            'Icon'           => $this->icon,
        ];
    }

}
