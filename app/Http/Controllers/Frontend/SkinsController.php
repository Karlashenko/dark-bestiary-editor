<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Skin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SkinsController
 *
 * @package App\Http\Controllers\Frontend
 */
class SkinsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Skin::orderBy('label')->get()->toArray());
    }

    /**
     * @param Skin $skin
     *
     * @return Response
     */
    public function show(Skin $skin): Response
    {
        return \response()->json($skin->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $skin = Skin::populate(new Skin(), $request);
        return \response()->json($skin->toArray());
    }

    /**
     * @param Request $request
     * @param Skin    $skin
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Skin $skin): Response
    {
        $skin = Skin::populate($skin, $request);
        return \response()->json($skin->toArray());
    }

    /**
     * @param Skin $skin
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Skin $skin): Response
    {
        $skin->delete();
        return \response()->json((object) []);
    }
}
