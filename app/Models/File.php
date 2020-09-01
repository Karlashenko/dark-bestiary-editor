<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class File
 *
 * @package App\Models
 */
class File extends Model
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'files';
    }

    /**
     * @inheritdoc
     */
    public function delete(): ?bool
    {
        $result = parent::delete();

        if (\file_exists($this->path)) {
            \unlink($this->path);
        }

        return $result;
    }

}
