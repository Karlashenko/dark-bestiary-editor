<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DialogueAction extends Model implements Presentable
{
    public const TYPES = [
        'NextDialogueAction',
        'ExitDialogueAction'
    ];

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'dialogue_actions';
        $this->timestamps = false;
    }

    /**
     * @return BelongsTo
     */
    public function textI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'text_i18n_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Type'           => (string) $this->type,
            'TextKey'        => $this->textI18n->key,
            'NextDialogueId' => (int) $this->next_dialogue_id,
        ];
    }

}
