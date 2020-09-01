<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Dialogue;
use App\Models\I18N;
use Illuminate\Console\Command;

/**
 * Class FixDialogues
 *
 * @package App\Console\Commands
 */
class FixDialogues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dialogues:fix';

    public function handle(): void
    {
        foreach (I18N::get() as $i18n) {
            if (strpos($i18n->key, 'temp_') === false) {
                continue;
            }

            $i18n->key = str_replace('temp_', '', $i18n->key);
            $i18n->save();

            echo $i18n->key . PHP_EOL;
        }

        exit;

        foreach (Dialogue::get() as $dialogue) {
            $label = 'temp_dialogue_' . strtolower($dialogue->label);

            if ($dialogue->title_i18n_id !== null) {
                $dialogue->titleI18n->key = $label . '_title';
                $dialogue->titleI18n->save();
            }

            if ($dialogue->text_i18n_id !== null) {
                $dialogue->textI18n->key = $label . '_description';
                $dialogue->textI18n->save();
            }

            echo $dialogue->titleI18n->key . PHP_EOL;
            echo $dialogue->textI18n->key . PHP_EOL;

            foreach ($dialogue->actions as $key => $action) {
                /** @var DialogueAction $action */

                if ($action->text_i18n_id === null) {
                    continue;
                }

                if ($action->textI18n->key === 'dialogue_goodbye') {
                    continue;
                }

                $action->textI18n->key = $label . '_answer_' . ($key + 1);
                $action->textI18n->save();

                echo $action->textI18n->key . PHP_EOL;
            }

            echo '----------------------------------------------' . PHP_EOL;
        }
    }
}
