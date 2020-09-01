<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Formula extends Model
{
    /**
     * @param Formula $formula
     * @param Request $request
     *
     * @return Formula
     */
    public static function populate(Formula $formula, Request $request): self
    {
        $formula->name = (string) $request->name;
        $formula->text = (string) $request->text;
        $formula->formula_id = $request->formula_id;
        $formula->save();

        return $formula;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = 'formulas';
        $this->timestamps = false;
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'formula_id')->withDefault();
    }

    public function getFormula(string $extra = ''): string
    {
        $parts = [];

        if ($this->formula->text) {
            $parts[] = "({$this->formula->getFormula()})";
        }

        $parts[] = $this->text;

        $parts = array_filter($parts);
        $parts = array_map(function ($part) {return trim($part);}, $parts);

        $formula = implode(' ', $parts);

        if ($extra === '') {
            return $formula;
        }

        if ($formula === '') {
            return $extra;
        }

        return "($formula) " . $extra;
    }
}
