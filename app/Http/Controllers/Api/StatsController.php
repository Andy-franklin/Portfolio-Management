<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\PortfolioSnapshot;
use App\Models\Position;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller
{
    public function store(Request $request)
    {
        $snapshot = PortfolioSnapshot::create([
            'user_id' => Auth::user()->id
        ]);

        foreach ($json = $request->json()->get(1)['positions'] as $positionId => $positionData) {
            $company = Company::where(['ticker_212' => $positionData['code']])->first();
            $companyId = null;
            if ($company !== null) {
                $companyId = $company->id;
            }

            Position::create([
                'position_id' => $positionData['positionId'],
                'average_price' => $positionData['averagePrice'],
                'average_price_converted' => $positionData['averagePriceConverted'],
                'current_price' => $positionData['currentPrice'],
                'value' => $positionData['value'],
                'investment' => $positionData['investment'],
                'margin' => $positionData['margin'],
                'ppl' => $positionData['ppl'],
                'quantity' => $positionData['quantity'],
                'active' => 1,
                'last_held' => new \DateTime(),
                'ticker_212' => $positionData['code'],
                'company_id' => $companyId,
                'portfolio_snapshot_id' => $snapshot->id
            ]);
        }

        return new JsonResponse([
            'message' => 'ok'
        ]);
    }
}
