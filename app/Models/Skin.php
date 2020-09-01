<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Skin extends Model implements Presentable
{
    public const PARTS = [
        'Kilt',
        'BootsLeft',
        'BootsRight',
        'GlovesLeft',
        'GlovesRight',
        'ArmorCenter',
        'ArmorLeft01',
        'ArmorLeft02',
        'ArmorLeft03',
        'ArmorRight01',
        'ArmorRight02',
        'ArmorRight03',
        'PantsCenter',
        'PantsLeft01',
        'PantsLeft02',
        'PantsRight01',
        'PantsRight02',
    ];

    /**
     * @param Skin    $property
     * @param Request $request
     *
     * @return Property
     */
    public static function populate(Skin $skin, Request $request): self
    {
        $skin->label = (string) $request->label;
        $skin->parts = (array) $request->parts;
        $skin->save();

        return $skin;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'skins';
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['parts'] = 'array';

        return $casts;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'    => $this->id,
            'Parts' => $this->parts,
        ];
    }
}
