<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\TalentCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TalentCategoriesController
 *
 * @package App\Http\Controllers\Frontend
 */
class TalentCategoriesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(TalentCategory::orderBy('index')->get()->toArray());
    }

    /**
     * @param TalentCategory $talentCategory
     *
     * @return Response
     */
    public function show(TalentCategory $talentCategory): Response
    {
        return \response()->json($talentCategory->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $talentCategory = TalentCategory::populate(new TalentCategory(), $request);
        return \response()->json($talentCategory->toArray());
    }

    /**
     * @param Request        $request
     * @param TalentCategory $talentCategory
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(Request $request, TalentCategory $talentCategory): Response
    {
        $talentCategory = TalentCategory::populate($talentCategory, $request);
        return \response()->json($talentCategory->toArray());
    }

    /**
     * @param TalentCategory $talentCategory
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(TalentCategory $talentCategory): Response
    {
        $talentCategory->delete();
        return \response()->json((object) []);
    }
}
