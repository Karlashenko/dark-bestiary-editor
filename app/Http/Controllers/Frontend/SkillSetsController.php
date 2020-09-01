<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\SkillSet;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SkillSetsController
 *
 * @package App\Http\Controllers\Frontend
 */
class SkillSetsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(SkillSet::query()
                                      ->select('skill_sets.*')
                                      ->join('i18n as i18n', 'skill_sets.name_i18n_id', '=', 'i18n.id', 'left')
                                      ->orderBy('i18n.key')
                                      ->get()
                                      ->toArray());
    }

    /**
     * @param SkillSet $skillSet
     *
     * @return Response
     */
    public function show(SkillSet $skillSet): Response
    {
        return \response()->json($skillSet->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $skillSet = SkillSet::populate(new SkillSet(), $request);

        return \response()->json($skillSet->toArray());
    }

    /**
     * @param Request $request
     * @param SkillSet $skillSet
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, SkillSet $skillSet): Response
    {
        $skillSet = SkillSet::populate($skillSet, $request);

        return \response()->json($skillSet->toArray());
    }

    /**
     * @param SkillSet $skillSet
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(SkillSet $skillSet): Response
    {
        $skillSet->delete();

        return \response()->json((object) []);
    }
}
