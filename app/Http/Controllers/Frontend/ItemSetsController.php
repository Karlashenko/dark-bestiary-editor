<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\ItemSet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemSetsController
 *
 * @package App\Http\Controllers\Frontend
 */
class ItemSetsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(ItemSet::orderBy('id')->get()->toArray());
    }

    /**
     * @param ItemSet $itemSet
     *
     * @return Response
     */
    public function show(ItemSet $itemSet): Response
    {
        return \response()->json($itemSet->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $itemSet = ItemSet::populate(new ItemSet(), $request);

        return \response()->json($itemSet->toArray());
    }

    /**
     * @param Request $request
     * @param ItemSet $itemSet
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, ItemSet $itemSet): Response
    {
        $itemSet = ItemSet::populate($itemSet, $request);

        return \response()->json($itemSet->toArray());
    }

    /**
     * @param ItemSet $itemSet
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(ItemSet $itemSet): Response
    {
        $itemSet->delete();

        return \response()->json((object) []);
    }
}
