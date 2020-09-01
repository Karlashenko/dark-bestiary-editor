<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use Exception;
use App\Models\Food;
use App\Http\Requests\FoodRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FoodController
 *
 * @package App\Http\Controllers\Frontend
 */
class FoodController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Food::orderBy('type')->get()->toArray());
    }

    /**
     * @param Food $food
     *
     * @return Response
     */
    public function show(Food $food): Response
    {
        return \response()->json($food->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $food = Food::populate(new Food(), $request);
        return \response()->json($food->toArray());
    }

    /**
     * @param Request $request
     * @param Food    $food
     *
     * @return Response
     */
    public function update(Request $request, Food $food): Response
    {
        $food = Food::populate($food, $request);
        return \response()->json($food->toArray());
    }

    /**
     * @param Food $food
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Food $food): Response
    {
        $food->delete();
        return \response()->json((object) []);
    }
}
