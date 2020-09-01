<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\AchievementCondition;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AchievementConditionsController
 *
 * @package App\Http\Controllers\Frontend
 */
class AchievementConditionsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(AchievementCondition::orderBy('id')->get()->toArray());
    }

    /**
     * @param AchievementCondition $achievementCondition
     *
     * @return Response
     */
    public function show(AchievementCondition $achievementCondition): Response
    {
        return \response()->json($achievementCondition->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $achievementCondition = AchievementCondition::populate(new AchievementCondition(), $request);
        return \response()->json($achievementCondition->toArray());
    }

    /**
     * @param Request              $request
     * @param AchievementCondition $achievementCondition
     *
     * @return Response
     */
    public function update(Request $request, AchievementCondition $achievementCondition): Response
    {
        $achievementCondition = AchievementCondition::populate($achievementCondition, $request);
        return \response()->json($achievementCondition->toArray());
    }

    /**
     * @param AchievementCondition $achievementCondition
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(AchievementCondition $achievementCondition): Response
    {
        $achievementCondition->delete();
        return \response()->json((object) []);
    }
}
