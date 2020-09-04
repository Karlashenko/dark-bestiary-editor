<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\I18NRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Class I18N
 *
 * @package App\Models
 */
class I18N extends Model implements Presentable
{
    /**
     * @param I18N        $i18n
     * @param I18NRequest $request
     *
     * @return I18N
     * @throws \Throwable
     */
    public static function populate(I18N $i18n, I18NRequest $request): self
    {
        DB::transaction(function () use ($i18n, $request) {
            $i18n->key = (string) $request->key;
            $i18n->en = (string) $request->en;
            $i18n->ru = (string) $request->ru;
            $i18n->save();

            $i18n->variables()->delete();

            foreach ((array) $request->get('variables', []) as $data) {
                $variable = new I18NVariable();
                $variable->i18n_id = $i18n->id;
                $variable->entity_id = \array_get($data, 'entity_id');
                $variable->entity_type = \array_get($data, 'entity_type');
                $variable->property_name = \array_get($data, 'property_name');
                $variable->save();
            }

            $i18n->load($i18n->with);
        });

        return $i18n;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'i18n';
        $this->timestamps = false;
        $this->with = ['variables'];
    }

    /**
     * @return HasMany
     */
    public function variables(): HasMany
    {
        return $this->hasMany(I18NVariable::class, 'i18n_id')->orderBy('id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'        => $this->id,
            'Key'       => $this->key,
            'Variables' => $this->variables->present(),
        ];
    }
}
