<?php

declare(strict_types=1);

namespace App\Http\Controllers\Frontend;

use App\Models\Currency;
use App\Http\Requests\CurrencyRequest;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CurrenciesController
 *
 * @package App\Http\Controllers\Frontend
 */
class CurrenciesController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        return \response()->json(Currency::orderBy('id')->get()->toArray());
    }

    /**
     * @param Currency $currency
     *
     * @return Response
     */
    public function show(Currency $currency): Response
    {
        return \response()->json($currency->toArray());
    }

    /**
     * @param CurrencyRequest $request
     *
     * @return Response
     */
    public function store(CurrencyRequest $request): Response
    {
        $currency = Currency::populate(new Currency(), $request);
        return \response()->json($currency->toArray());
    }

    /**
     * @param CurrencyRequest $request
     * @param Currency        $currency
     *
     * @return Response
     */
    public function update(CurrencyRequest $request, Currency $currency): Response
    {
        $currency = Currency::populate($currency, $request);
        return \response()->json($currency->toArray());
    }

    /**
     * @param Currency $currency
     *
     * @return Response
     * @throws \Exception
     */
    public function destroy(Currency $currency): Response
    {
        $currency->delete();
        return \response()->json((object) []);
    }
}
