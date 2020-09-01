<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\SkillCategoryRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

/**
 * Class SkillCategory
 *
 * @package App\Models
 */
class SkillCategory extends Model implements Presentable
{
    /**
     * @param SkillCategory $skillCategory
     * @param Request       $request
     *
     * @return SkillCategory
     */
    public static function populate(SkillCategory $skillCategory, Request $request): self
    {
        $skillCategory->index = (int) $request->index;
        $skillCategory->name_i18n_id = $request->name_i18n_id;
        $skillCategory->save();

        return $skillCategory;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'skill_categories';
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
