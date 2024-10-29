<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function postEvent(Request $request)
    {
        $id = $request->get('destination');
        $amount = $request->get('amount');
        $operation = $request->get('type');

        if ($operation == 'deposit') {
            $value = Cache::increment($id, $amount);

            $result = [
                'destination' => [
                    'id' => $id,
                    'balance' => $value,
                ],
            ];
        }

        if ($operation == 'withdraw') {
            $id = $request->get('origin');
            $value = Cache::get($id);

            if ($value == null) {
                return response()->json(0, Response::HTTP_NOT_FOUND);
            }
            $value = Cache::decrement($id, $amount);

            $result = [
                'origin' => [
                    'id' => $id,
                    'balance' => $value,
                ],
            ];

            return response()->json($result, Response::HTTP_CREATED);
        }

        if ($operation == 'transfer') {
            $id = $request->get('origin');
            $destination = $request->get('destination');

            $totalValue = Cache::get($id);
            if ($totalValue == null) {
                return response()->json(0, Response::HTTP_NOT_FOUND);
            }

            $originValue = Cache::decrement($id, $amount);
            $value = Cache::increment($destination, $amount);

            $result = [
                'origin' => [
                    'id' => $id,
                    'balance' => $originValue,
                ],
                'destination' => [
                    'id' => $destination,
                    'balance' => $value,
                ],
            ];
        }

        return response()->json($result, Response::HTTP_OK);

    }
}
