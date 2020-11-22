<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function store(Request $request)
    {
        foreach ($json = $request->json() as $accountId => $account) {
            if (isset($json[$accountId]['tradingType']) && $json[$accountId]['tradingType'] === 'EQUITY') {
                // Only handle the EQUITY trading account.
                // Save with the account ID and type just so that we can handle others later if we want to.

            }
        }

        return new JsonResponse([
            'message' => 'ok'
        ]);
    }
}
