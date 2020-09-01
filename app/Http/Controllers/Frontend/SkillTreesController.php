<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\SkillTree;
use App\Http\Requests\SkillTreeRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SkillTreesController
 *
 * @package App\Http\Controllers\Frontend
 */
class SkillTreesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(SkillTree::orderBy('id')->get()->toArray());
    }

    /**
     * @param SkillTree $skillTree
     *
     * @return Response
     */
    public function show(SkillTree $skillTree): Response
    {
        return \response()->json($skillTree->toArray());
    }

    /**
     * @param SkillTreeRequest $request
     *
     * @return Response
     * @throws \Throwable
     * @throws \Exception
     */
    public function store(SkillTreeRequest $request): Response
    {
        $skillTree = SkillTree::populate(new SkillTree(), $request);
        return \response()->json($skillTree->toArray());
    }

    /**
     * @param SkillTreeRequest $request
     * @param SkillTree        $skillTree
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(SkillTreeRequest $request, SkillTree $skillTree): Response
    {
        $skillTree = SkillTree::populate($skillTree, $request);
        return \response()->json($skillTree->toArray());
    }

    /**
     * @param SkillTree $skillTree
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(SkillTree $skillTree): Response
    {
        $skillTree->delete();
        return \response()->json((object) []);
    }
}
