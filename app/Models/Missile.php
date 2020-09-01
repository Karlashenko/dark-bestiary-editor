<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\MissileRequest;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Missile
 *
 * @package App\Models
 */
class Missile extends Model implements Presentable
{
    public const MOVER_TYPES = [
        'Linear',
        'Directional',
        'Curve',
        'Parabolic',
    ];

    /**
     * @param Missile        $missile
     * @param MissileRequest $request
     *
     * @return Missile
     * @throws \Exception
     * @throws \Throwable
     */
    public static function populate(Missile $missile, MissileRequest $request): self
    {
        $missile->name = $request->name;
        $missile->model = $request->model;
        $missile->impact_prefab = (string) $request->impact_prefab;
        $missile->save();

        return $missile;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $missiles = [])
    {
        parent::__construct($missiles);

        $this->table = 'missiles';
        $this->timestamps = false;
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'           => $this->id,
            'Name'         => $this->name,
            'Prefab'       => $this->model,
            'ImpactPrefab' => $this->impact_prefab,
        ];
    }

}
