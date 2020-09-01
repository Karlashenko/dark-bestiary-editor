<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\RecipeRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Recipe
 *
 * @package App\Models
 */
class Recipe extends Model implements Presentable
{
    public const CATEGORIES = [
        'Alchemy',
        'Blacksmith',
    ];

    /**
     * @param Recipe        $recipe
     * @param RecipeRequest $request
     *
     * @return Recipe
     */
    public static function populate(Recipe $recipe, RecipeRequest $request): self
    {
        $recipe->item_id = $request->item_id;
        $recipe->item_count = $request->item_count;
        $recipe->blueprint_id = $request->blueprint_id;
        $recipe->is_unlocked = (bool) $request->is_unlocked;
        $recipe->is_custom = (bool) $request->is_custom;
        $recipe->category = (string) $request->category;
        $recipe->save();

        $recipe->ingredients()->sync([]);

        foreach ((array) $request->get('ingredients', []) as $ingredient) {
            $id = (int) \array_get($ingredient, 'id');
            $count = (int) \array_get($ingredient, 'pivot.count');

            $recipe->ingredients()->attach($id, ['count' => $count]);
        }

        $recipe->load($recipe->with);

        return $recipe;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $recipes = [])
    {
        parent::__construct($recipes);

        $this->table = 'recipes';
        $this->timestamps = false;
        $this->with = ['item', 'ingredients'];
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function blueprint(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'blueprint_id');
    }

    /**
     * @return BelongsToMany
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'recipes_ingredients')
                    ->withPivot(['id', 'count'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'Category'       => $this->category ?: 'None',
            'IsUnlocked'     => $this->is_unlocked,
            'ItemId'         => (int) $this->item_id,
            'BlueprintId'    => (int) $this->blueprint_id,
            'ItemCount'      => $this->item_count,
            'Ingredients'    => $this->ingredients->map(function (Item $item) {
                return [
                    'ItemId' => $item->id,
                    'Count'  => $item->pivot->count
                ];
            }),
        ];
    }

}
