<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderItemController extends Controller
{
    public function getOrderItems(Request $request)
    {
        $orderItem = OrderItem::orderBy('id', 'DESC')->get();
        $orderItem->load('menu');
        $orderItem->load('order');

        if ($orderItem) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'List Data Order',
                    'data' => $orderItem
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }

    public function getOrderItemsByOrder($id)
    {
        $orderItem = OrderItem::where("order_id", "=", $id)->orderBy('id', 'DESC')->get();
        $orderItem->load('menu');
        $orderItem->load('order');

        if ($orderItem) {
            $response = [
                'status' => 'Success',
                'message' => 'List Data Order',
                'data' => $orderItem
            ];
            return response()->json($response);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }

    public function getOrderItem($id)
    {
        $orderItem = OrderItem::find($id);
        $orderItem->load('menu');
        $orderItem->load('order');

        if ($orderItem) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'Data Order',
                    'data' => $orderItem
                ],
                200
            );
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Data gagal ditemukan",
            ], 400);
        }
    }
}
