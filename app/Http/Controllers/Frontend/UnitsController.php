<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Unit;
use App\Http\Requests\UnitRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UnitsController
 *
 * @package App\Http\Controllers\Frontend
 */
class UnitsController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function index(Request $request): Response
    {
        $units = Unit::orderBy('id')->get();

        return \response()->json($units->toArray());
    }

    /**
     * @param Unit $unit
     *
     * @return Response
     */
    public function show(Unit $unit): Response
    {
        return \response()->json($unit->toArray());
    }

    /**
     * @param UnitRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(UnitRequest $request): Response
    {
        $unit = Unit::populate(new Unit(), $request);
        return \response()->json($unit->toArray());
    }

    /**
     * @param UnitRequest $request
     * @param Unit        $unit
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(UnitRequest $request, Unit $unit): Response
    {
        $unit = Unit::populate($unit, $request);
        return \response()->json($unit->toArray());
    }

    /**
     * @param Unit $unit
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Unit $unit): Response
    {
        $unit->delete();
        return \response()->json((object) []);
    }
}
