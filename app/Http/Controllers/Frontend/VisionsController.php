<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use Exception;
use Throwable;
use App\Models\Vision;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VisionsController
 *
 * @package App\Http\Controllers\Frontend
 */
class VisionsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Vision::orderByDesc('probability')->get()->toArray());
    }

    /**
     * @param Vision $vision
     *
     * @return Response
     */
    public function show(Vision $vision): Response
    {
        return \response()->json($vision->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws Throwable
     */
    public function store(Request $request): Response
    {
        $vision = Vision::populate(new Vision(), $request);
        return \response()->json($vision->toArray());
    }

    /**
     * @param Request $request
     * @param Vision  $vision
     *
     * @return Response
     * @throws Throwable
     */
    public function update(Request $request, Vision $vision): Response
    {
        $vision = Vision::populate($vision, $request);
        return \response()->json($vision->toArray());
    }

    /**
     * @param Vision $vision
     *
     * @return Response
     * @throws Exception
     */
    public function destroy(Vision $vision): Response
    {
        $vision->delete();
        return \response()->json((object) []);
    }
}
