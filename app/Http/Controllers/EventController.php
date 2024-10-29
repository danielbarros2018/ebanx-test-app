<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class EventController extends Controller
{
    public function postEvent(Request $request)
    {
        $operation = $request->get('type');
        switch ($operation) {
            case 'deposit':
                $result = $this->deposit($request);
                break;

            case 'withdraw':
                $result = $this->withdraw($request);
                break;

            case 'transfer':
                $result = $this->transfer($request);
                break;

        }

        if (empty($result)) {
            return response()->json(0, Response::HTTP_NOT_FOUND);
        }

        return response()->json($result, Response::HTTP_CREATED);

    }

    private function deposit(Request $request)
    {
        $id = $request->get('destination');
        $amount = $request->get('amount');

        $value = Cache::increment($id, $amount);

        $result = [
            'destination' => [
                'id' => $id,
                'balance' => $value,
            ],
        ];

        return $result;
    }

    private function withdraw(Request $request): array
    {
        $id = $request->get('origin');
        $amount = $request->get('amount');
        $value = Cache::get($id);

        if ($value == null) {
            return [];
        }

        $value = Cache::decrement($id, $amount);

        $result = [
            'origin' => [
                'id' => $id,
                'balance' => $value,
            ],
        ];

        return $result;
    }

    private function transfer(Request $request): array
    {
        $id = $request->get('origin');
        $destination = $request->get('destination');
        $amount = $request->get('amount');

        $totalValue = Cache::get($id);
        if ($totalValue == null) {
            return [];
        }

        $originValue = Cache::decrement($id, $amount);
        $destinationValue = Cache::increment($destination, $amount);

        $result = [
            'origin' => [
                'id' => $id,
                'balance' => $originValue,
            ],
            'destination' => [
                'id' => $destination,
                'balance' => $destinationValue,
            ],
        ];

        return $result;
    }
}
