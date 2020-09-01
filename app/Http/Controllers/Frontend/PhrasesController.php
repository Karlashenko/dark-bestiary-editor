<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Phrase;
use App\Http\Requests\PhraseRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PhrasesController
 *
 * @package App\Http\Controllers\Frontend
 */
class PhrasesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Phrase::orderBy('label')->get()->toArray());
    }

    /**
     * @param Phrase $phrase
     *
     * @return Response
     */
    public function show(Phrase $phrase): Response
    {
        return \response()->json($phrase->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $phrase = Phrase::populate(new Phrase(), $request);

        return \response()->json($phrase->toArray());
    }

    /**
     * @param Request   $request
     * @param Phrase $phrase
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Phrase $phrase): Response
    {
        $phrase = Phrase::populate($phrase, $request);

        return \response()->json($phrase->toArray());
    }

    /**
     * @param Phrase $phrase
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Phrase $phrase): Response
    {
        $phrase->delete();

        return \response()->json((object) []);
    }
}
