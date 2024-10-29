<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class BalanceController extends Controller
{
    public function getBalance(Request $request)
    {
        $account_id = $request->get('account_id');
        $value = Cache::get($account_id);

        if ($value == null) {
            return response()->json(0, Response::HTTP_NOT_FOUND);
        }

        return $value;
    }
}
