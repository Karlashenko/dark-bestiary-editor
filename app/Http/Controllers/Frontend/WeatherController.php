<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Weather;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WeatherController
 *
 * @package App\Http\Controllers\Frontend
 */
class WeatherController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Weather::orderBy('id')->get()->toArray());
    }

    /**
     * @param Weather $weather
     *
     * @return Response
     */
    public function show(Weather $weather): Response
    {
        return \response()->json($weather->toArray());
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function store(Request $request): Response
    {
        $weather = Weather::populate(new Weather(), $request);
        return \response()->json($weather->toArray());
    }

    /**
     * @param Request $request
     * @param Weather $weather
     *
     * @return Response
     */
    public function update(Request $request, Weather $weather): Response
    {
        $weather = Weather::populate($weather, $request);
        return \response()->json($weather->toArray());
    }

    /**
     * @param Weather $weather
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Weather $weather): Response
    {
        $weather->delete();
        return \response()->json((object) []);
    }
}
