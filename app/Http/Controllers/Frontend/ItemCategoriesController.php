<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\ItemCategory;
use App\Http\Requests\ItemCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemCategoriesController
 *
 * @package App\Http\Controllers\Frontend
 */
class ItemCategoriesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(ItemCategory::query()
                                             ->select('item_categories.*')
                                             ->join('i18n as i18n', 'item_categories.name_i18n_id', '=', 'i18n.id', 'left')
                                             ->orderBy('i18n.en')
                                             ->get()
                                             ->toArray());
    }

    /**
     * @param ItemCategory $itemCategory
     *
     * @return Response
     */
    public function show(ItemCategory $itemCategory): Response
    {
        return \response()->json($itemCategory->toArray());
    }

    /**
     * @param \Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $itemCategory = ItemCategory::populate(new ItemCategory(), $request);

        return \response()->json($itemCategory->toArray());
    }

    /**
     * @param \Request     $request
     * @param ItemCategory $itemCategory
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, ItemCategory $itemCategory): Response
    {
        $itemCategory = ItemCategory::populate($itemCategory, $request);

        return \response()->json($itemCategory->toArray());
    }

    /**
     * @param ItemCategory $itemCategory
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(ItemCategory $itemCategory): Response
    {
        $itemCategory->delete();

        return \response()->json((object) []);
    }
}
