<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\ItemModifier;
use App\Http\Requests\ItemModifierRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ItemModifiersController
 *
 * @package App\Http\Controllers\Frontend
 */
class ItemModifiersController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(ItemModifier::query()
                                             ->select('item_modifiers.*')
                                             ->join('i18n as i18n', 'item_modifiers.suffix_i18n_id', '=', 'i18n.id', 'left')
                                             ->orderBy('i18n.en')
                                             ->orderBy('label')
                                             ->get()
                                             ->toArray());
    }

    /**
     * @param ItemModifier $itemModifier
     *
     * @return Response
     */
    public function show(ItemModifier $itemModifier): Response
    {
        return \response()->json($itemModifier->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $itemModifier = ItemModifier::populate(new ItemModifier(), $request);

        return \response()->json($itemModifier->toArray());
    }

    /**
     * @param Request      $request
     * @param ItemModifier $itemModifier
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, ItemModifier $itemModifier): Response
    {
        $itemModifier = ItemModifier::populate($itemModifier, $request);

        return \response()->json($itemModifier->toArray());
    }

    /**
     * @param ItemModifier $itemModifier
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(ItemModifier $itemModifier): Response
    {
        $itemModifier->delete();

        return \response()->json((object) []);
    }
}
