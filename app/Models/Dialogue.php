<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Dialogue extends Model implements Presentable
{
    public const NARRATORS = [
        'Character',
        'Arcanist',
        'Blacksmith',
        'Vendor',
        'Tavern',
        'RatEater',
        'FairyDragon',
        'Hunter',
        'Banderlog',
        'Amaroq',
        'Ent',
        'BaldFairy',
        'Bullywug',
        'Banshee',
        'Reanimator',
        'Warden',
        'ManInBlack',
        'VillagerA',
        'VillagerB',
        'VillagerC',
    ];

    /**
     * @param Dialogue $item
     * @param Request  $request
     *
     * @return Dialogue
     * @throws \Throwable
     */
    public static function populate(Dialogue $dialogue, Request $request): self
    {
        DB::transaction(function () use ($dialogue, $request) {
            $dialogue->is_parent = (bool) $request->is_parent;
            $dialogue->label = (string) $request->label;
            $dialogue->narrator = (string) $request->narrator;
            $dialogue->title_i18n_id = $request->title_i18n_id;
            $dialogue->text_i18n_id = $request->text_i18n_id;
            $dialogue->save();

            $dialogue->actions()->delete();

            foreach ((array) $request->get('actions', []) as $data) {
                $action = new DialogueAction();
                $action->dialogue_id = $dialogue->id;
                $action->type = (string) \array_get($data, 'type');
                $action->text_i18n_id = \array_get($data, 'text_i18n_id');
                $action->next_dialogue_id = \array_get($data, 'next_dialogue_id');
                $action->save();
            }

            $dialogue->validators()->sync([]);

            foreach ((array) $request->get('validators', []) as $behaviour) {
                $id = (int) \array_get($behaviour, 'id');

                if ($id === 0) {
                    continue;
                }

                $dialogue->validators()->attach($id);
            }
        });

        $dialogue->load($dialogue->with);

        return $dialogue;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'dialogues';
        $this->timestamps = false;
        $this->with = ['actions', 'validators'];
    }

    /**
     * @return BelongsTo
     */
    public function titleI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'title_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function textI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'text_i18n_id')->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function actions(): HasMany
    {
        return $this->hasMany(DialogueAction::class, 'dialogue_id')->orderBy('id');
    }

    /**
     * @return BelongsToMany
     */
    public function validators(): BelongsToMany
    {
        return $this->belongsToMany(Validator::class, 'dialogues_validators')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'         => $this->id,
            'IsParent'   => $this->is_parent,
            'Narrator'   => $this->narrator,
            'TitleKey'   => $this->titleI18n->key,
            'TextKey'    => $this->textI18n->key,
            'Validators' => $this->validators->pluck('id'),
            'Actions'    => $this->actions->present(),
        ];
    }
}
