<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\SkillCategoryRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * Class TalentCategory
 *
 * @package App\Models
 */
class TalentCategory extends Model implements Presentable
{
    /**
     * @param TalentCategory $skillCategory
     * @param Request        $request
     *
     * @return TalentCategory
     */
    public static function populate(TalentCategory $talentCategory, Request $request): self
    {
        $talentCategory->index = (int) $request->index;
        $talentCategory->name_i18n_id = $request->name_i18n_id;
        $talentCategory->save();

        return $talentCategory;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'talent_categories';
        $this->timestamps = false;
        $this->with = ['nameI18n'];
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'      => $this->id,
            'Index'   => $this->index,
            'NameKey' => $this->nameI18n->key,
        ];
    }

}
