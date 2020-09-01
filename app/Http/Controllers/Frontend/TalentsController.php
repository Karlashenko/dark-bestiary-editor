<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Talent;
use App\Http\Requests\TalentRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TalentsController
 *
 * @package App\Http\Controllers\Frontend
 */
class TalentsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Talent::orderBy('category_id')
                                       ->orderBy('tier')
                                       ->orderBy('index')
                                       ->get()
                                       ->toArray());
    }

    /**
     * @param Talent $talent
     *
     * @return Response
     */
    public function show(Talent $talent): Response
    {
        return \response()->json($talent->toArray());
    }

    /**
     * @param TalentRequest $request
     *
     * @return Response
     */
    public function store(TalentRequest $request): Response
    {
        $talent = Talent::populate(new Talent(), $request);
        return \response()->json($talent->toArray());
    }

    /**
     * @param TalentRequest $request
     * @param Talent        $talent
     *
     * @return Response
     */
    public function update(TalentRequest $request, Talent $talent): Response
    {
        $talent = Talent::populate($talent, $request);
        return \response()->json($talent->toArray());
    }

    /**
     * @param Talent $talent
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Talent $talent): Response
    {
        $talent->delete();
        return \response()->json((object) []);
    }
}
