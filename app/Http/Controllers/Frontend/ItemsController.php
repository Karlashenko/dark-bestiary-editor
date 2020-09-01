<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Item;
use App\Http\Requests\ItemRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemsController
 *
 * @package App\Http\Controllers\Frontend
 */
class ItemsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Item::query()
                                     ->select('items.*')
                                     ->join('i18n as i18n', 'items.name_i18n_id', '=', 'i18n.id', 'left')
                                     ->orderBy('i18n.en')
                                     ->get()
                                     ->toArray());
    }

    /**
     * @param Item $item
     *
     * @return Response
     */
    public function show(Item $item): Response
    {
        return \response()->json($item->toArray());
    }

    /**
     * @param ItemRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(ItemRequest $request): Response
    {
        $item = Item::populate(new Item(), $request);

        return \response()->json($item->toArray());
    }

    /**
     * @param ItemRequest $request
     * @param Item        $item
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(ItemRequest $request, Item $item): Response
    {
        $item = Item::populate($item, $request);

        return \response()->json($item->toArray());
    }

    /**
     * @param Item $item
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Item $item): Response
    {
        $item->delete();

        return \response()->json((object) []);
    }
}
