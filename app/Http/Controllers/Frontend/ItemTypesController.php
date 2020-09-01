<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\ItemType;
use App\Http\Requests\ItemTypeRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemTypesController
 *
 * @package App\Http\Controllers\Frontend
 */
class ItemTypesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(ItemType::orderByDesc('equipment_strategy_type')->orderBy('type')->get()->toArray());
    }

    /**
     * @param ItemType $itemType
     *
     * @return Response
     */
    public function show(ItemType $itemType): Response
    {
        return \response()->json($itemType->toArray());
    }

    /**
     * @param \Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $itemType = ItemType::populate(new ItemType(), $request);

        return \response()->json($itemType->toArray());
    }

    /**
     * @param \Request     $request
     * @param ItemType $itemType
     *
     * @return Response
     */
    public function update(Request $request, ItemType $itemType): Response
    {
        $itemType = ItemType::populate($itemType, $request);

        return \response()->json($itemType->toArray());
    }

    /**
     * @param ItemType $itemType
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(ItemType $itemType): Response
    {
        $itemType->delete();

        return \response()->json((object) []);
    }
}
