<?php

declare(strict_types=1);

namespace App;

/**
 * Interface Presentable
 *
 * @package App
 */
interface Presentable
{
    /**
     * @return array
     */
    public function present(): array;
}
