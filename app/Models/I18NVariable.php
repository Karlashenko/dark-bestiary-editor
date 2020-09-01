<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class I18NVariable
 *
 * @package App\Models
 */
class I18NVariable extends Model implements Presentable
{
    public const ENTITY_TYPES = [
        'Effect',
        'Behaviour',
        'Mastery',
    ];

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'i18n_variables';
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'EntityId'     => $this->entity_id,
            'EntityType'   => $this->entity_type,
            'PropertyName' => $this->property_name,
        ];
    }

}
