<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Attribute;
use App\Http\Requests\AttributeRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AttributesController
 *
 * @package App\Http\Controllers\Frontend
 */
class AttributesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Attribute::orderBy('index')->get()->toArray());
    }

    /**
     * @param Attribute $attribute
     *
     * @return Response
     */
    public function show(Attribute $attribute): Response
    {
        return \response()->json($attribute->toArray());
    }

    /**
     * @param AttributeRequest $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(AttributeRequest $request): Response
    {
        $attribute = Attribute::populate(new Attribute(), $request);
        return \response()->json($attribute->toArray());
    }

    /**
     * @param AttributeRequest $request
     * @param Attribute        $attribute
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(AttributeRequest $request, Attribute $attribute): Response
    {
        $attribute = Attribute::populate($attribute, $request);
        return \response()->json($attribute->toArray());
    }

    /**
     * @param Attribute $attribute
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Attribute $attribute): Response
    {
        $attribute->delete();
        return \response()->json((object) []);
    }
}
