<?php

use App\Models\DialogueAction;
use App\Models\I18N;
use App\Models\Item;
use App\Models\Phrase;

Artisan::command('durp', function () {
    foreach (\App\Models\Behaviour::whereType('AuraBehaviour')->get() as $behaviour) {
        $behaviourName = $behaviour->nameI18n->en . ' ' . $behaviour->label;
        $aura = \App\Models\Behaviour::find($behaviour->aura_behaviour_id);

        if ($aura === null) {
            echo $behaviourName . ' is missing aura behaviour.' . PHP_EOL;
            continue;
        }

        echo $behaviourName . ' ' . $behaviour->stack_count_max . ' -> ' . $aura->stack_count_max . PHP_EOL;
    }
});
