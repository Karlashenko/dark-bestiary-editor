<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class RewardsController
 *
 * @package App\Http\Controllers\Frontend
 */
class RewardsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Reward::orderBy('label')->get()->toArray());
    }

    /**
     * @param Reward $reward
     *
     * @return Response
     */
    public function show(Reward $reward): Response
    {
        return \response()->json($reward->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $reward = Reward::populate(new Reward(), $request);
        return \response()->json($reward->toArray());
    }

    /**
     * @param Request $request
     * @param Reward  $reward
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, Reward $reward): Response
    {
        $reward = Reward::populate($reward, $request);
        return \response()->json($reward->toArray());
    }

    /**
     * @param Reward $reward
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Reward $reward): Response
    {
        $reward->delete();
        return \response()->json((object) []);
    }
}
