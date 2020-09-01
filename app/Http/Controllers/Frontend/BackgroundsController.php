<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Background;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BackgroundsController
 *
 * @package App\Http\Controllers\Frontend
 */
class BackgroundsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Background::orderBy('id')->get()->toArray());
    }

    /**
     * @param Background $background
     *
     * @return Response
     */
    public function show(Background $background): Response
    {
        return \response()->json($background->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $background = Background::populate(new Background(), $request);
        return \response()->json($background->toArray());
    }

    /**
     * @param Request    $request
     * @param Background $background
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Background $background): Response
    {
        $background = Background::populate($background, $request);
        return \response()->json($background->toArray());
    }

    /**
     * @param Background $background
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Background $background): Response
    {
        $background->delete();
        return \response()->json((object) []);
    }
}
