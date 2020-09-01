<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Relic;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RelicsController
 *
 * @package App\Http\Controllers\Frontend
 */
class RelicsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Relic::orderBy('id')->get()->toArray());
    }

    /**
     * @param Relic $relic
     *
     * @return Response
     */
    public function show(Relic $relic): Response
    {
        return \response()->json($relic->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $relic = Relic::populate(new Relic(), $request);
        return \response()->json($relic->toArray());
    }

    /**
     * @param Request $request
     * @param Relic  $relic
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Relic $relic): Response
    {
        $relic = Relic::populate($relic, $request);
        return \response()->json($relic->toArray());
    }

    /**
     * @param Relic $relic
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Relic $relic): Response
    {
        $relic->delete();
        return \response()->json((object) []);
    }
}
