<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model implements Presentable
{
    public const POINTS = [
        'None',
        'Auto',
        'Origin',
        'OverHead',
        'Chest',
        'Head',
        'Mask',
        'GunBarrel',
        'LeftHand',
        'RightHand',
        'LeftFist',
        'RightFist',
        'LeftFoot',
        'RightFoot',
        'Shield',
        'Belt',
        'RightShoulder',
        'LeftShoulder',
        'Back',
        'Special01',
        'Special02',
        'Special03',
    ];

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'attachments';
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Prefab'             => (string) $this->prefab,
            'Target'             => (string) $this->target ?: 'Target',
            'Point'              => (string) $this->point ?: 'None',
            'Rotate'             => (bool) $this->rotate,
            'RotateSameAsCaster' => (bool) $this->rotate_same_as_caster,
            'FlipRotation'       => (bool) $this->flip_rotation,
            'OriginalScale'      => (bool) $this->original_scale,
        ];
    }

}
