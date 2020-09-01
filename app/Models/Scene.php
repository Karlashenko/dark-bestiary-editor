<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;

class Scene extends Model implements Presentable
{
    public const ENTRANCES = [
        'Top',
        'Right',
        'Bottom',
        'Left',
        'Center',
    ];

    /**
     * @param Scene   $scene
     * @param Request $request
     *
     * @return Scene
     */
    public static function populate(Scene $scene, Request $request): self
    {
        $scene->environment_id = $request->environment_id;
        $scene->is_scripted = (bool) $request->is_scripted;
        $scene->has_no_exit = (bool) $request->has_no_exit;
        $scene->label = (string) $request->label;
        $scene->prefab = (string) $request->prefab;
        $scene->save();

        return $scene;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'scenes';
        $this->timestamps = false;
        $this->with = ['environment'];
    }

    /**
     * @return BelongsToMany
     */
    public function episodes(): BelongsToMany
    {
        return $this->belongsToMany(Episode::class, 'episodes_scenes', 'scene_id', 'episode_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsTo
     */
    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class, 'environment_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'          => $this->id,
            'HasNoExit'   => (bool) $this->has_no_exit,
            'IsScripted'  => (bool) $this->is_scripted,
            'Environment' => $this->environment->present(),
            'Prefab'      => (string) $this->prefab,
        ];
    }

}
