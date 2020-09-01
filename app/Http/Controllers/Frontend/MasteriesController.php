<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Mastery;
use App\Http\Requests\MasteryRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MasteriesController
 *
 * @package App\Http\Controllers\Frontend
 */
class MasteriesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Mastery::query()
                                        ->select('masteries.*')
                                        ->join('i18n as i18n', 'masteries.name_i18n_id', '=', 'i18n.id', 'left')
                                        ->orderBy('i18n.en')
                                        ->get()
                                        ->toArray());
    }

    /**
     * @param Mastery $mastery
     *
     * @return Response
     */
    public function show(Mastery $mastery): Response
    {
        return \response()->json($mastery->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $mastery = Mastery::populate(new Mastery(), $request);

        return \response()->json($mastery->toArray());
    }

    /**
     * @param Request $request
     * @param Mastery $mastery
     *
     * @return Response
     */
    public function update(Request $request, Mastery $mastery): Response
    {
        $mastery = Mastery::populate($mastery, $request);

        return \response()->json($mastery->toArray());
    }

    /**
     * @param Mastery $mastery
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Mastery $mastery): Response
    {
        $mastery->delete();

        return \response()->json((object) []);
    }
}
