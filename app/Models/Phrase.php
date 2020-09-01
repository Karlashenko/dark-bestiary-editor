<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class Phrase extends Model implements Presentable
{
    /**
     * @param Phrase  $phrase
     * @param Request $request
     *
     * @return Phrase
     */
    public static function populate(Phrase $phrase, Request $request): self
    {
        $phrase->label = (string) $request->label;
        $phrase->narrator = (string) $request->narrator;
        $phrase->text_i18n_id = $request->text_i18n_id;
        $phrase->save();

        $phrase->load($phrase->with);

        return $phrase;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'phrases';
        $this->timestamps = false;
        $this->with = ['textI18n'];
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
            'Id'       => $this->id,
            'Label'    => $this->label,
            'Narrator' => $this->narrator,
            'TextKey'  => $this->textI18n->key,
        ];
    }
}
