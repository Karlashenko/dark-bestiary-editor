<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Environment;
use App\Http\Requests\EnvironmentRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class EnvironmentsController
 *
 * @package App\Http\Controllers\Frontend
 */
class EnvironmentsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Environment::orderBy('index')->get()->toArray());
    }

    /**
     * @param Environment $environment
     *
     * @return Response
     */
    public function show(Environment $environment): Response
    {
        return \response()->json($environment->toArray());
    }

    /**
     * @param EnvironmentRequest $request
     *
     * @return Response
     */
    public function store(EnvironmentRequest $request): Response
    {
        $environment = Environment::populate(new Environment(), $request);
        return \response()->json($environment->toArray());
    }

    /**
     * @param EnvironmentRequest $request
     * @param Environment        $environment
     *
     * @return Response
     */
    public function update(EnvironmentRequest $request, Environment $environment): Response
    {
        $environment = Environment::populate($environment, $request);
        return \response()->json($environment->toArray());
    }

    /**
     * @param Environment $environment
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Environment $environment): Response
    {
        $environment->delete();
        return \response()->json((object) []);
    }
}
