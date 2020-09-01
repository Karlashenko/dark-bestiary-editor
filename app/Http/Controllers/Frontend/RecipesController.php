<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Recipe;
use App\Http\Requests\RecipeRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RecipesController
 *
 * @package App\Http\Controllers\Frontend
 */
class RecipesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Recipe::get()->sort(function (Recipe $a, Recipe $b) {
            return str_pad((string) $a->item->level, 3, '0', STR_PAD_LEFT) . $a->item->nameI18n->en <=>
                str_pad((string) $b->item->level, 3, '0', STR_PAD_LEFT) . $b->item->nameI18n->en;
        })->values()->toArray());
    }

    /**
     * @param Recipe $recipe
     *
     * @return Response
     */
    public function show(Recipe $recipe): Response
    {
        return \response()->json($recipe->toArray());
    }

    /**
     * @param RecipeRequest $request
     *
     * @return Response
     */
    public function store(RecipeRequest $request): Response
    {
        $recipe = Recipe::populate(new Recipe(), $request);
        return \response()->json($recipe->toArray());
    }

    /**
     * @param RecipeRequest $request
     * @param Recipe        $recipe
     *
     * @return Response
     */
    public function update(RecipeRequest $request, Recipe $recipe): Response
    {
        $recipe = Recipe::populate($recipe, $request);
        return \response()->json($recipe->toArray());
    }

    /**
     * @param Recipe $recipe
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Recipe $recipe): Response
    {
        $recipe->delete();
        return \response()->json((object) []);
    }
}
