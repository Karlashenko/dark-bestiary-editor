<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Archetype;
use App\Http\Requests\ArchetypeRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ArchetypesController
 *
 * @package App\Http\Controllers\Frontend
 */
class ArchetypesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Archetype::orderBy('id')->get()->toArray());
    }

    /**
     * @param Archetype $archetype
     *
     * @return Response
     */
    public function show(Archetype $archetype): Response
    {
        return \response()->json($archetype->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $archetype = Archetype::populate(new Archetype(), $request);

        return \response()->json($archetype->toArray());
    }

    /**
     * @param Request   $request
     * @param Archetype $archetype
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Archetype $archetype): Response
    {
        $archetype = Archetype::populate($archetype, $request);

        return \response()->json($archetype->toArray());
    }

    /**
     * @param Archetype $archetype
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Archetype $archetype): Response
    {
        $archetype->delete();

        return \response()->json((object) []);
    }
}
