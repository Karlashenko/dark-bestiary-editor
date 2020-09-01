<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SkillSet extends Model implements Presentable
{
    /**
     * @param SkillSet $skillSet
     * @param Request $request
     *
     * @return SkillSet
     * @throws \Throwable
     */
    public static function populate(SkillSet $skillSet, Request $request): self
    {
        DB::transaction(function () use ($skillSet, $request) {
            $skillSet->index = (int) $request->index;
            $skillSet->name_i18n_id = $request->name_i18n_id;
            $skillSet->icon = (string) $request->icon;
            $skillSet->save();

            $skillSet->behaviours()->sync([]);

            foreach ((array) $request->get('behaviours', []) as $behaviour) {
                $skillSet->behaviours()->attach(
                    (int) \array_get($behaviour, 'id'),
                    ['skill_count' => (int) \array_get($behaviour, 'pivot.skill_count')]
                );
            }

            $skillSet->skills()->sync([]);

            foreach ((array) $request->get('skills', []) as $skill) {
                $skillSet->skills()->attach((int) \array_get($skill, 'id'));
            }

            $skillSet->load($skillSet->with);
        });

        return $skillSet;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'skill_sets';
        $this->timestamps = false;
        $this->with = ['behaviours', 'nameI18n', 'skills'];
    }

    /**
     * @return BelongsToMany
     */
    public function behaviours(): BelongsToMany
    {
        return $this->belongsToMany(Behaviour::class, 'skill_sets_behaviours')
                    ->withPivot(['id', 'skill_count'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'skill_sets_skills')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        $behaviours = $this->behaviours->groupBy(function (Behaviour $behaviour) {return $behaviour->pivot->skill_count;})
                                       ->map(function ($group, $key) {
                                           return [
                                               'SkillCount' => (int) $key,
                                               'Behaviours' => $group->map(function ($behaviour) {return $behaviour->id;}),
                                           ];
                                       })->values();

        return [
            'Id'         => $this->id,
            'Icon'       => $this->icon,
            'NameKey'    => $this->nameI18n->key,
            'Skills'     => $this->skills->pluck('id')->toArray(),
            'Behaviours' => $behaviours,
        ];
    }
}
