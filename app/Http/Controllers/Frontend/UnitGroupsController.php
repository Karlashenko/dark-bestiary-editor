<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\UnitGroup;
use App\Http\Requests\UnitGroupRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UnitGroupsController
 *
 * @package App\Http\Controllers\Frontend
 */
class UnitGroupsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(UnitGroup::orderBy('id')->get()->toArray());
    }

    /**
     * @param UnitGroup $unitGroup
     *
     * @return Response
     */
    public function show(UnitGroup $unitGroup): Response
    {
        return \response()->json($unitGroup->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $unitGroup = UnitGroup::populate(new UnitGroup(), $request);
        return \response()->json($unitGroup->toArray());
    }

    /**
     * @param Request   $request
     * @param UnitGroup $unitGroup
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, UnitGroup $unitGroup): Response
    {
        $unitGroup = UnitGroup::populate($unitGroup, $request);
        return \response()->json($unitGroup->toArray());
    }

    /**
     * @param UnitGroup $unitGroup
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(UnitGroup $unitGroup): Response
    {
        $unitGroup->delete();
        return \response()->json((object) []);
    }
}
