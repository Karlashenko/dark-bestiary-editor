<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Formula;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FormulasController
 *
 * @package App\Http\Controllers\Frontend
 */
class FormulasController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Formula::orderBy('name')->get());
    }

    /**
     * @param Formula $formula
     *
     * @return Response
     */
    public function show(Formula $formula): Response
    {
        return \response()->json($formula->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function store(Request $request): Response
    {
        $formula = Formula::populate(new Formula(), $request);
        return \response()->json($formula->toArray());
    }

    /**
     * @param Request $request
     * @param Formula $formula
     *
     * @return Response
     * @throws \Exception
     * @throws \Throwable
     */
    public function update(Request $request, Formula $formula): Response
    {
        $formula = Formula::populate($formula, $request);
        return \response()->json($formula->toArray());
    }

    /**
     * @param Formula $formula
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Formula $formula): Response
    {
        $formula->delete();
        return \response()->json((object) []);
    }
}
