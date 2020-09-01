<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Dialogue;
use App\Models\I18N;
use App\Models\Item;
use App\Models\ItemModifier;
use App\Models\Recipe;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;
use test\Mockery\Fixtures\MethodWithVoidReturnType;

/**
 * Class IndexController
 *
 * @package App\Http\Controllers
 */
class IndexController extends Controller
{
    /**
     * @return View
     */
    public function index(): View
    {
        return \view('index');
    }

    /**
     * @return View
     */
    public function importIndex(): View
    {
        return \view('import');
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function importStore(Request $request): Response
    {
        $parts = explode("\r\n", $request->data);
        $counter = 0;

        foreach ($parts as $index => $key) {
            if ($counter > 0) {
                $counter--;
                continue;
            }

            $counter = 3;

            $i18n = I18N::where('key', $key)->first();

            if ($i18n === null) {
                echo 'Missing: ' . $key . '<br/>';
                continue;
            }

            if ($i18n->ru === $parts[$index + 2]) {
                continue;
            }

            echo $key . '<br/>';
            echo $parts[$index + 2] . '<br/>';
            echo $i18n->ru . '<br/>';
            echo '-----------------<br/>';
        }

        exit;

        $data = json_decode($request->data);

        $keys = [];

        foreach ($data as $datum) {
            $i18n = I18N::where('key', $datum->key)->first();

            if ($i18n === null) {
                continue;
            }

            echo $i18n->key . '<br/>';
            echo $i18n->ru . '<br/>';
            echo '<br/>';
            echo $datum->ru . '<br/>';
            echo '<hr/>';
        }

        foreach ($data as $string) {
            $i18n = I18N::where('key', $string->key)->first();

            if ($i18n == null) {
                continue;
            }

            $i18n->ru = $string->ru;
            $i18n->save();
        }

        return \response()->json();
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function importStore_Translations(Request $request): Response
    {
        $data = json_decode($request->data);

        $keys = [];

        foreach ($data as $datum) {
            $i18n = I18N::where('key', $datum->key)->first();

            if ($i18n === null) {
                continue;
            }

            echo $i18n->key . '<br/>';
            echo $i18n->ru . '<br/>';
            echo '<br/>';
            echo $datum->ru . '<br/>';
            echo '<hr/>';
        }

        foreach ($data as $string) {
            $i18n = I18N::where('key', $string->key)->first();

            if ($i18n == null) {
                continue;
            }

            $i18n->ru = $string->ru;
            $i18n->save();
        }

        return \response()->json();
    }

    /**
     * @return Response
     */
    public function test(): Response
    {
        foreach (I18N::orderBy('key')->get() as $translation) {
            echo $translation->key . '<br/><br/>';
            echo $translation->en . '<br/>';
            echo '--------------------------------------------------------</br>';
        }

        exit;
    }

    /**
     * @return Response
     */
    public function translation(): Response
    {
        return response()->json(I18N::where('en', DB::raw('ru'))->orderBy('key')->get()->map(function (I18N $i18N) {
            return [
                'key' => $i18N->key,
                'ru'  => $i18N->ru,
                'en'  => $i18N->en,
            ];
        }));

        $dialogues = [];

        foreach (Dialogue::orderBy('label')->get() as $dialogue) {
            /**
             * @var Dialogue $dialogue
             */
            $dialogues[] = $dialogue->titleI18n;
            $dialogues[] = $dialogue->textI18n;

            foreach ($dialogue->actions as $action) {
                $dialogues[] = $action->textI18n;
            }
        }

        $dialogues = (new Collection($dialogues))->filter(function (I18N $i18n) {
            return $i18n->key;
        })->unique(function (I18N $i18n) {
            return $i18n->key;
        })->map(function (I18N $i18N) {
            return [
                'key' => $i18N->key,
                'ru'  => $i18N->ru,
                'en'  => $i18N->en,
            ];
        })->values();

        $other = I18N::where('en', DB::raw('ru'))->where('key', 'not ilike', '%dialogue%')->orderBy('key')->get()->map(function (I18N $i18N) {
            return [
                'key' => $i18N->key,
                'ru'  => $i18N->ru,
                'en'  => $i18N->en,
            ];
        });

        return \response()->json(($dialogues->merge($other)->toArray()));
    }
}
