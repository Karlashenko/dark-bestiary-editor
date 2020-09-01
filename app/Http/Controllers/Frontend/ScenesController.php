<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Scene;
use Illuminate\Http\Request;
use Illuminate\HttpRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ScenesController
 *
 * @package App\Http\Controllers\Frontend
 */
class ScenesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Scene::orderBy('label')->get()->toArray());
    }

    /**
     * @param Scene $scene
     *
     * @return Response
     */
    public function show(Scene $scene): Response
    {
        return \response()->json($scene->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        return \response()->json(Scene::populate(new Scene(), $request)->toArray());
    }

    /**
     * @param Request $request
     * @param Scene   $scene
     *
     * @return Response
     */
    public function update(Request $request, Scene $scene): Response
    {
        return \response()->json(Scene::populate($scene, $request)->toArray());
    }

    /**
     * @param Scene $scene
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Scene $scene): Response
    {
        $scene->delete();

        return \response()->json((object) []);
    }
}
