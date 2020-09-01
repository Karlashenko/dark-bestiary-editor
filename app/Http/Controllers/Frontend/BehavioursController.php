<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Behaviour;
use App\Http\Requests\BehaviourRequest;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BehavioursController
 *
 * @package App\Http\Controllers\Frontend
 */
class BehavioursController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Behaviour::query()
                                          ->select('behaviours.*',
                                              DB::raw('concat(i18n.en, behaviours.label, behaviours.suffix) as concat'))
                                          ->join('i18n as i18n', 'behaviours.name_i18n_id', '=', 'i18n.id', 'left')
                                          ->orderBy('concat')
                                          ->get()
                                          ->toArray());
    }

    /**
     * @param Behaviour $behaviour
     *
     * @return Response
     */
    public function show(Behaviour $behaviour): Response
    {
        return \response()->json($behaviour->toArray());
    }

    /**
     * @param BehaviourRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(BehaviourRequest $request): Response
    {
        $behaviour = Behaviour::populate(new Behaviour(), $request);

        return \response()->json($behaviour->toArray());
    }

    /**
     * @param BehaviourRequest $request
     * @param Behaviour        $behaviour
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(BehaviourRequest $request, Behaviour $behaviour): Response
    {
        $behaviour = Behaviour::populate($behaviour, $request);

        return \response()->json($behaviour->toArray());
    }

    /**
     * @param Behaviour $behaviour
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Behaviour $behaviour): Response
    {
        $behaviour->delete();

        return \response()->json((object) []);
    }
}
