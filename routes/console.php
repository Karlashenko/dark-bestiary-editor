<?php

use App\Models\DialogueAction;
use App\Models\I18N;
use App\Models\Item;
use App\Models\Phrase;

Artisan::command('durp', function () {
    foreach (\App\Models\Item::whereTypeId(21)->get() as $item) {
        echo $item->nameI18n->en . PHP_EOL;
    }
});
