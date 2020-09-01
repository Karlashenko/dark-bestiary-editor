<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Encounter extends Model implements Presentable
{
    public const TYPES = [
        'Empty',
        'Combat',
        'Treasure',
        'Talk',
    ];

    public const UNIT_SOURCE_TYPES = [
        'Table',
        'Environment',
        'UnitGroup',
    ];

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'encounters';
        $this->timestamps = false;
        $this->with = ['unitTable', 'unitGroups', 'phrases'];
    }

    /**
     * @return BelongsTo
     */
    public function unitTable(): BelongsTo
    {
        return $this->belongsTo(UnitTable::class, 'unit_table_id')->withDefault(['units' => []]);
    }

    /**
     * @return BelongsToMany
     */
    public function unitGroups(): BelongsToMany
    {
        return $this->belongsToMany(UnitGroup::class, 'encounters_unit_groups')
                    ->withPivot('id')
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function phrases(): BelongsToMany
    {
        return $this->belongsToMany(Phrase::class, 'encounters_phrases')
                    ->withPivot('id')
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Type'                     => (string) $this->type,
            'UnitSourceType'           => (string) $this->unit_source_type,
            'UnitGroupChallengeRating' => (int) $this->unit_group_challenge_rating,
            'UnitGroupEnvironmentId'   => (int) $this->unit_group_environment_id,
            'UnitTable'                => $this->unitTable->present(),
            'UnitGroups'               => $this->unitGroups->pluck('id')->toArray(),
            'Phrases'                  => $this->phrases->pluck('id')->toArray(),
            'StartPhraseId'            => (int) $this->start_phrase_id,
            'CompletePhraseId'         => (int) $this->complete_phrase_id,
        ];
    }
}
