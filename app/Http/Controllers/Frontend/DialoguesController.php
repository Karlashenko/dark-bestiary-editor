<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Dialogue;
use App\Http\Requests\DialogueRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DialoguesController
 *
 * @package App\Http\Controllers\Frontend
 */
class DialoguesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Dialogue::orderBy('label')->get()->toArray());
    }

    /**
     * @param Dialogue $dialogue
     *
     * @return Response
     */
    public function show(Dialogue $dialogue): Response
    {
        return \response()->json($dialogue->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $dialogue = Dialogue::populate(new Dialogue(), $request);

        return \response()->json($dialogue->toArray());
    }

    /**
     * @param Request   $request
     * @param Dialogue $dialogue
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Dialogue $dialogue): Response
    {
        $dialogue = Dialogue::populate($dialogue, $request);

        return \response()->json($dialogue->toArray());
    }

    /**
     * @param Dialogue $dialogue
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Dialogue $dialogue): Response
    {
        $dialogue->delete();

        return \response()->json((object) []);
    }
}
