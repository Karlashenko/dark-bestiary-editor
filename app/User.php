<?php

declare(strict_types=1);

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Class User
 *
 * @package App
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->fillable = [
            'name',
            'email',
            'password',
        ];

        $this->hidden = [
            'password',
            'remember_token',
        ];
    }

}
