<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\I18N;
use App\Http\Requests\I18NRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class I18NController
 *
 * @package App\Http\Controllers\Frontend
 */
class I18NController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(I18N::query()->orderBy('key')->get()->toArray());
    }

    /**
     * @param I18N $i18n
     *
     * @return Response
     */
    public function show(I18N $i18n): Response
    {
        return \response()->json($i18n->toArray());
    }

    /**
     * @param I18NRequest $request
     *
     * @return Response
     * @throws \Throwable
     */
    public function store(I18NRequest $request): Response
    {
        $i18n = I18N::populate(new I18N(), $request);
        return \response()->json($i18n->toArray());
    }

    /**
     * @param I18NRequest $request
     * @param I18N        $i18n
     *
     * @return Response
     * @throws \Throwable
     */
    public function update(I18NRequest $request, I18N $i18n): Response
    {
        $i18n = I18N::populate($i18n, $request);
        return \response()->json($i18n->toArray());
    }

    /**
     * @param I18N $i18n
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(I18N $i18n): Response
    {
        $i18n->delete();
        return \response()->json((object) []);
    }
}
