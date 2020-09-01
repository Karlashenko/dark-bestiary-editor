<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\SkillCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SkillCategoriesController
 *
 * @package App\Http\Controllers\Frontend
 */
class SkillCategoriesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(SkillCategory::orderBy('index')->get()->toArray());
    }

    /**
     * @param SkillCategory $skillCategory
     *
     * @return Response
     */
    public function show(SkillCategory $skillCategory): Response
    {
        return \response()->json($skillCategory->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $skillCategory = SkillCategory::populate(new SkillCategory(), $request);
        return \response()->json($skillCategory->toArray());
    }

    /**
     * @param Request $request
     * @param SkillCategory        $skillCategory
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, SkillCategory $skillCategory): Response
    {
        $skillCategory = SkillCategory::populate($skillCategory, $request);
        return \response()->json($skillCategory->toArray());
    }

    /**
     * @param SkillCategory $skillCategory
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(SkillCategory $skillCategory): Response
    {
        $skillCategory->delete();
        return \response()->json((object) []);
    }
}
