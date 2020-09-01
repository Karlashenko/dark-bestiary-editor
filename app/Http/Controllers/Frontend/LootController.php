<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Loot;
use App\Http\Requests\LootRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class LootController
 *
 * @package App\Http\Controllers\Frontend
 */
class LootController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Loot::orderBy('name')->get()->toArray());
    }

    /**
     * @param Loot $loot
     *
     * @return Response
     */
    public function show(Loot $loot): Response
    {
        return \response()->json($loot->toArray());
    }

    /**
     * @param LootRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(LootRequest $request): Response
    {
        $loot = Loot::populate(new Loot(), $request);
        return \response()->json($loot->toArray());
    }

    /**
     * @param LootRequest $request
     * @param Loot        $loot
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(LootRequest $request, Loot $loot): Response
    {
        $loot = Loot::populate($loot, $request);
        return \response()->json($loot->toArray());
    }

    /**
     * @param Loot $loot
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Loot $loot): Response
    {
        $loot->delete();
        return \response()->json((object) []);
    }
}
