<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Skill;
use App\Http\Requests\SkillRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SkillsController
 *
 * @package App\Http\Controllers\Frontend
 */
class SkillsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Skill::query()
                                      ->select('skills.*')
                                      ->join('i18n as i18n', 'skills.name_i18n_id', '=', 'i18n.id', 'left')
                                      ->orderBy('prefix')
                                      ->orderBy('type')
                                      ->orderBy('i18n.en')
                                      ->get()
                                      ->toArray());
    }

    /**
     * @param Skill $skill
     *
     * @return Response
     */
    public function show(Skill $skill): Response
    {
        return \response()->json($skill->toArray());
    }

    /**
     * @param SkillRequest $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(SkillRequest $request): Response
    {
        $skill = Skill::populate(new Skill(), $request);
        return \response()->json($skill->toArray());
    }

    /**
     * @param SkillRequest $request
     * @param Skill        $skill
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(SkillRequest $request, Skill $skill): Response
    {
        $skill = Skill::populate($skill, $request);
        return \response()->json($skill->toArray());
    }

    /**
     * @param Skill $skill
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Skill $skill): Response
    {
        $skill->delete();
        return \response()->json((object) []);
    }
}
