<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Weather extends Model implements Presentable
{
    /**
     * @param Weather $weather
     * @param Request $request
     *
     * @return Weather
     */
    public static function populate(Weather $weather, Request $request): self
    {
        $weather->type = (string) $request->type;
        $weather->prefab = (string) $request->prefab;
        $weather->ambient = (string) $request->ambient;
        $weather->save();

        return $weather;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'weather';
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'      => $this->id,
            'Name'    => $this->name,
            'Prefab'  => $this->prefab,
            'Ambient' => $this->ambient,
        ];
    }

}
