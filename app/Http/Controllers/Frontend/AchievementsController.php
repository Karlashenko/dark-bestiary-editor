<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Achievement;
use App\Http\Requests\AchievementRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AchievementsController
 *
 * @package App\Http\Controllers\Frontend
 */
class AchievementsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Achievement::orderBy('index')->get()->toArray());
    }

    /**
     * @param Achievement $achievement
     *
     * @return Response
     */
    public function show(Achievement $achievement): Response
    {
        return \response()->json($achievement->toArray());
    }

    /**
     * @param AchievementRequest $request
     *
     * @return Response
     */
    public function store(AchievementRequest $request): Response
    {
        $achievement = Achievement::populate(new Achievement(), $request);
        return \response()->json($achievement->toArray());
    }

    /**
     * @param AchievementRequest $request
     * @param Achievement        $achievement
     *
     * @return Response
     */
    public function update(AchievementRequest $request, Achievement $achievement): Response
    {
        $achievement = Achievement::populate($achievement, $request);
        return \response()->json($achievement->toArray());
    }

    /**
     * @param Achievement $achievement
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Achievement $achievement): Response
    {
        $achievement->delete();
        return \response()->json((object) []);
    }
}
