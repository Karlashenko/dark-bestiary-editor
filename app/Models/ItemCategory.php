<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemCategory extends Model implements Presentable
{
    /**
     * @param Request      $request
     * @param ItemCategory $category
     *
     * @return ItemCategory
     * @throws \Throwable
     */
    public static function populate(ItemCategory $category, Request $request): self
    {
        DB::transaction(function () use ($category, $request) {
            $category->name_i18n_id = $request->name_i18n_id;
            $category->type = $request->type;
            $category->save();

            $category->types()->sync([]);

            foreach ((array) $request->get('types', []) as $type) {
                $id = (int) \array_get($type, 'id');

                if ($id === 0) {
                    continue;
                }

                $category->types()->attach($id);
            }

            $category->load($category->with);
        });

        return $category;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'item_categories';
        $this->with = ['nameI18n', 'types'];
        $this->timestamps = false;
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(ItemType::class, 'item_categories_item_types')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'      => $this->id,
            'NameKey' => $this->nameI18n->key,
            'Type'    => $this->type,

            'ItemTypes' => $this->types->map(function (ItemType $itemType) {
                return $itemType->id;
            }),
        ];
    }

}
