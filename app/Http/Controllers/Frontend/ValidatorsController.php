<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Validator;
use App\Http\Requests\ValidatorRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ValidatorsController
 *
 * @package App\Http\Controllers\Frontend
 */
class ValidatorsController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Validator::orderBy('name')->get()->toArray());
    }

    /**
     * @param Validator $validator
     *
     * @return Response
     */
    public function show(Validator $validator): Response
    {
        return \response()->json($validator->toArray());
    }

    /**
     * @param ValidatorRequest $request
     *
     * @return Response
     * @throws \Throwable
     * @throws \Exception
     */
    public function store(ValidatorRequest $request): Response
    {
        $validator = Validator::populate(new Validator(), $request);
        return \response()->json($validator->toArray());
    }

    /**
     * @param ValidatorRequest $request
     * @param Validator        $validator
     *
     * @return Response
     * @throws \Throwable
     * @throws \Exception
     */
    public function update(ValidatorRequest $request, Validator $validator): Response
    {
        $validator = Validator::populate($validator, $request);
        return \response()->json($validator->toArray());
    }

    /**
     * @param Validator $validator
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Validator $validator): Response
    {
        $validator->delete();
        return \response()->json((object) []);
    }
}
