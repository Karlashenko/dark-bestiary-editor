<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Effect;
use App\Http\Requests\EffectRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EffectsController
 *
 * @package App\Http\Controllers\Frontend
 */
class EffectsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Effect::orderBy('name')->get()->toArray());
    }

    /**
     * @param Effect $effect
     *
     * @return Response
     */
    public function show(Effect $effect): Response
    {
        return \response()->json($effect->toArray());
    }

    /**
     * @param EffectRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(EffectRequest $request): Response
    {
        $effect = Effect::populate(new Effect(), $request);
        return \response()->json($effect->toArray());
    }

    /**
     * @param EffectRequest $request
     * @param Effect        $effect
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(EffectRequest $request, Effect $effect): Response
    {
        $effect = Effect::populate($effect, $request);
        return \response()->json($effect->toArray());
    }

    /**
     * @param Effect $effect
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Effect $effect): Response
    {
        $effect->delete();
        return \response()->json((object) []);
    }
}
