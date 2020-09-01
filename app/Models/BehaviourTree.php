<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\BehaviourTreeNodeRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class BehaviourTree
 *
 * @package App\Models
 */
class BehaviourTree extends Model implements Presentable
{
    public const DICTIONARY = [
        'Composite' => [
            'Tree',
            'Template',
            'TemplateReference',
            'Sequence',
            'Selector',
            'Parallel',
            'Race',
        ],

        'Decorator' => [
            'Inverter',
            'Succeeder',
        ],

        'Condition' => [
            'Chance',
            'CanMove',
            'CanUseSkill',
            'CanApproachTargetEntity',
            'AnyCorpsesInRange',
            'IsMyTurn',
            'IsImSlowed',
            'IsTargetEntityAlive',
            'IsTargetEntityBehind',
            'IsTargetEntityReachable',
            'IsTargetEntityInRange',
            'IsTargetEntityInRangeSkill',
            'IsTargetEntityOnLineOfSight',
            'IsTargetEntitySlowed',
            'IsTargetEntityStunned',
            'IsTargetEntityImmobilized',
            'IsTargetEntitySet',
            'IsTargetEntityAlly',
            'IsTargetPointReachable',
            'IsTargetPointInRangeSkill',
            'IsTargetPointWalkable',
            'IsTargetPointSet',
            'IsTargetHealthBelow',
            'IsCountOfEnemiesInRangeGreater',
            'IsHealthBelow',
        ],

        'Task' => [
            'Wait',
            'UseSkillOnTargetEntity',
            'UseSkillOnTargetPoint',
            'UseSkillOnTargetPointCorpse',
            'UseSkillOnSelf',
            'MoveToTargetEntity',
            'MoveToTargetPoint',
            'SetTargetEntitySelf',
            'SetTargetEntityNearestWoundedAlly',
            'SetTargetEntityNearestWoundedAllyExcludeSelf',
            'SetTargetEntityNearestEnemy',
            'SetTargetEntityNearestEnemySkill',
            'SetTargetEntityUnitOfType',
            'SetTargetEntityToPrevious',
            'SetTargetPointNearestEnemy',
            'SetTargetPointSummonSkill',
            'SetTargetPointEscapeSkill',
            'SetTargetPointRandomWithinRange',
            'SetTargetPointSuitableForSkillUseTargetEntity',
            'SetTargetPointSuitableForSkillUseTargetPoint',
            'SetTargetPointAroundTargetEntitySkill',
            'SetTargetPointTargetEntityPosition',
            'SetTargetPointBehindEnemy',
            'SetTargetPointNearestCorpse',
            'SetTargetPointKite',
            'ClearTargetEntity',
            'EndTurn',
        ],
    ];

    /**
     * @param BehaviourTree $node
     * @param Request       $request
     *
     * @return BehaviourTree
     * @throws \Throwable
     */
    public static function populate(BehaviourTree $node, Request $request): self
    {
        DB::transaction(function () use ($node, $request) {
            $node->populateArray($request->all())->save();
            $node->syncChildrenRecursive((array) $request->children);
            $node->load($node->with);
        });

        return $node;
    }

    /**
     * @param array              $data
     * @param BehaviourTree|null $parent
     *
     * @return BehaviourTree
     */
    public function populateArray(array $data, BehaviourTree $parent = null): self
    {
        $this->name = (string) \array_get($data, 'name');
        $this->comment = (string) \array_get($data, 'comment');
        $this->type = (string) \array_get($data, 'type');
        $this->group = (string) \array_get($data, 'group');
        $this->properties = (array) \array_get($data, 'properties');
        $this->parent_id = $parent !== null ? $parent->id : null;

        return $this;
    }

    /**
     * @param array $childrenData
     *
     * @return void
     */
    private function syncChildrenRecursive(array $childrenData): void
    {
        $this->children()->delete();

        $childrenIds = [];

        foreach ($childrenData as $childData) {
            $child = new BehaviourTree();
            $child->populateArray($childData, $this)->save();
            $child->syncChildrenRecursive((array) \array_get($childData, 'children'));

            $childrenIds[] = $child->id;
        }

        $this->children()->sync($childrenIds);
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'behaviour_trees';
        $this->timestamps = false;
        $this->with = ['children'];
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['properties'] = 'array';

        return $casts;
    }

    /**
     * @param Builder $builder
     * @param string  $type
     *
     * @return Builder
     */
    public function scopeByType(Builder $builder, string $type): Builder
    {
        if ($type === '') {
            return $builder;
        }

        return $builder->whereIn('type', \explode(',', $type));
    }

    /**
     * @param Builder $builder
     * @param string  $group
     *
     * @return Builder
     */
    public function scopeByGroup(Builder $builder, string $group): Builder
    {
        return $builder->where('group', $group);
    }

    /**
     * @return BelongsToMany
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'behaviour_trees_nodes', 'tree_id', 'node_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        $dictionary = [];

        foreach ($this->properties as $property) {
            $dictionary[$property['key']] = $property['value'];
        }

        $properties = [
            'SkillId'        => (int) \array_get($dictionary, 'skillId'),
            'UnitId'         => (int) \array_get($dictionary, 'unitId'),
            'WaitDuration'   => (float) \array_get($dictionary, 'duration'),
            'HealthFraction' => (float) \array_get($dictionary, 'healthFraction'),
            'Count'          => (int) \array_get($dictionary, 'count'),
            'Range'          => (int) \array_get($dictionary, 'range'),
            'Chance'         => (float) \array_get($dictionary, 'chance'),
        ];

        return [
            'Id'         => $this->id,
            'Type'       => $this->type,
            'Properties' => $properties,
            'Children'   => $this->presentChildren(),
        ];
    }

    private function presentChildren(): array
    {
        $presentation = [];

        foreach ($this->children as $child) {
            if ($child->type === 'TemplateReference') {
                $property = (new Collection($child->properties))->first(function (array $item) {
                    return $item['key'] === 'templateId';
                });

                $dictionary = [];

                foreach ($child->properties as $childProperty) {
                    $dictionary[$childProperty['key']] = $childProperty['value'];
                }

                $template = self::find($property['value']);
                $template->replaceChildProperties($dictionary);

                foreach ($template->present()['Children'] as $item) {
                    $presentation[] = $item;
                }

                continue;
            }

            $presentation[] = $child->present();
        }

        return $presentation;
    }

    private function replaceChildProperties(array $dictionary)
    {
        foreach ($this->children as $child) {
            $properties = [];

            foreach ($child->properties as $property) {
                foreach ($dictionary as $key => $value) {
                    if ($property['key'] !== $key) {
                        continue;
                    }

                    $property['value'] = $value;
                }

                $properties[] = $property;
            }

            $child->properties = $properties;
            $child->replaceChildProperties($dictionary);
        }
    }
}
