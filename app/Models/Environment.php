<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\EnvironmentRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Environment
 *
 * @package App\Models
 */
class Environment extends Model implements Presentable
{
    public const TYPES = [
        'Grassland',
        'Cave',
        'Forest',
        'Swamp',
        'Graveyard',
        'Dungeon',
    ];

    /**
     * @param Environment        $environment
     * @param EnvironmentRequest $request
     *
     * @return Environment
     * @throws \Throwable
     */
    public static function populate(Environment $environment, EnvironmentRequest $request): self
    {
        DB::transaction(function () use ($environment, $request) {
            $environment->index = (int) $request->index;
            $environment->name_i18n_id = $request->name_i18n_id;
            $environment->description_i18n_id = $request->description_i18n_id;
            $environment->ambience = (string) $request->ambience;
            $environment->music = (string) $request->music;
            $environment->save();

            $environment->weather()->sync([]);

            foreach ((array) $request->get('weather', []) as $item) {
                $environment->weather()->attach((int) \array_get($item, 'id'));
            }

            $environment->load($environment->with);
        });

        return $environment;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $environments = [])
    {
        parent::__construct($environments);

        $this->table = 'environments';
        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n', 'weather'];
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
     * @return BelongsToMany
     */
    public function weather(): BelongsToMany
    {
        return $this->belongsToMany(Weather::class, 'environments_weather')->orderBy('type');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'             => (int) $this->id,
            'Index'          => (int) $this->index,
            'NameKey'        => (string) $this->nameI18n->key,
            'DescriptionKey' => (string) $this->descriptionI18n->key,
            'Ambience'       => (string) $this->ambience,
            'Music'          => (string) $this->music,
            'Weather'        => $this->weather->present()->toArray(),
        ];
    }

}
