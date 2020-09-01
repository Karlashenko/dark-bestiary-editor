<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Missile;
use App\Http\Requests\MissileRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MissilesController
 *
 * @package App\Http\Controllers\Frontend
 */
class MissilesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Missile::orderBy('id')->get()->toArray());
    }

    /**
     * @param Missile $missile
     *
     * @return Response
     */
    public function show(Missile $missile): Response
    {
        return \response()->json($missile->toArray());
    }

    /**
     * @param MissileRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(MissileRequest $request): Response
    {
        $missile = Missile::populate(new Missile(), $request);
        return \response()->json($missile->toArray());
    }

    /**
     * @param MissileRequest $request
     * @param Missile        $missile
     *
     * @return Response
     * @throws \Throwable
     * @throws \Exception
     */
    public function update(MissileRequest $request, Missile $missile): Response
    {
        $missile = Missile::populate($missile, $request);
        return \response()->json($missile->toArray());
    }

    /**
     * @param Missile $missile
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Missile $missile): Response
    {
        $missile->delete();
        return \response()->json((object) []);
    }
}
