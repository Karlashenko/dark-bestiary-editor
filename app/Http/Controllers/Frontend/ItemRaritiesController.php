<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\ItemRarity;
use App\Http\Requests\ItemRarityRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemRaritiesController
 *
 * @package App\Http\Controllers\Frontend
 */
class ItemRaritiesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(ItemRarity::orderBy('index')->get()->toArray());
    }

    /**
     * @param ItemRarity $itemRarity
     *
     * @return Response
     */
    public function show(ItemRarity $itemRarity): Response
    {
        return \response()->json($itemRarity->toArray());
    }

    /**
     * @param \Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $itemRarity = ItemRarity::populate(new ItemRarity(), $request);

        return \response()->json($itemRarity->toArray());
    }

    /**
     * @param \Request     $request
     * @param ItemRarity $itemRarity
     *
     * @return Response
     */
    public function update(Request $request, ItemRarity $itemRarity): Response
    {
        $itemRarity = ItemRarity::populate($itemRarity, $request);

        return \response()->json($itemRarity->toArray());
    }

    /**
     * @param ItemRarity $itemRarity
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(ItemRarity $itemRarity): Response
    {
        $itemRarity->delete();

        return \response()->json((object) []);
    }
}
