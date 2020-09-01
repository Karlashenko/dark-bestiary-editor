<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Scenario;
use App\Http\Requests\ScenarioRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ScenariosController
 *
 * @package App\Http\Controllers\Frontend
 */
class ScenariosController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Scenario::orderBy('type')->orderBy('index')->get()->toArray());
    }

    /**
     * @param Scenario $scenario
     *
     * @return Response
     */
    public function show(Scenario $scenario): Response
    {
        return \response()->json($scenario->toArray());
    }

    /**
     * @param ScenarioRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(ScenarioRequest $request): Response
    {
        $scenario = Scenario::populate(new Scenario(), $request);
        return \response()->json($scenario->toArray());
    }

    /**
     * @param ScenarioRequest $request
     * @param Scenario        $scenario
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(ScenarioRequest $request, Scenario $scenario): Response
    {
        $scenario = Scenario::populate($scenario, $request);
        return \response()->json($scenario->toArray());
    }

    /**
     * @param Scenario $scenario
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Scenario $scenario): Response
    {
        $scenario->delete();
        return \response()->json((object) []);
    }
}
