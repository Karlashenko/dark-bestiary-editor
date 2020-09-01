<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Property;
use App\Http\Requests\PropertyRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PropertiesController
 *
 * @package App\Http\Controllers\Frontend
 */
class PropertiesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Property::orderBy('index')->get()->toArray());
    }

    /**
     * @param Property $property
     *
     * @return Response
     */
    public function show(Property $property): Response
    {
        return \response()->json($property->toArray());
    }

    /**
     * @param PropertyRequest $request
     *
     * @return Response
     */
    public function store(PropertyRequest $request): Response
    {
        $property = Property::populate(new Property(), $request);
        return \response()->json($property->toArray());
    }

    /**
     * @param PropertyRequest $request
     * @param Property        $property
     *
     * @return Response
     */
    public function update(PropertyRequest $request, Property $property): Response
    {
        $property = Property::populate($property, $request);
        return \response()->json($property->toArray());
    }

    /**
     * @param Property $property
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Property $property): Response
    {
        $property->delete();
        return \response()->json((object) []);
    }
}
