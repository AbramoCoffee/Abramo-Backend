<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrders()
    {
        $orders = Order::orderBy('id', 'DESC')->get();


        if ($orders) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'List data order ditemukan',
                    'data' => $orders
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 'Failed',
                    'message' => "Data order gagal ditemukan",
                ],
                400
            );
        }
    }

    public function getOrdersByTime($time)
    {
        $query = Order::query();

        switch ($time) {
            case 'today';
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday';
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'this_week';
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week';
                $query->whereBetween('created_at', [Carbon::now()->subWeek(), Carbon::now()]);
                break;
            case 'this_month';
                $query->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'last_month';
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month);
                break;
            case 'this_year';
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year';
                $query->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }

        $orders = $query->orderBy('id', 'DESC')->get();


        if ($orders) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'List Data Order',
                    'data' => $orders
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'status' => 'Failed',
                    'message' => "Data gagal ditemukan",
                ],
                400
            );
        }
    }

    public function createOrder(Request $request)
    {
        function calculateTotalAmount($orderedItems)
        {
            $totalAmount = 0;

            foreach ($orderedItems as $orderedItem) {

                $product = Product::where('id', "=", $orderedItem['product_id'])->first();

                $totalAmount += $product->price * $orderedItem['qty'];
            }

            return $totalAmount;
        }

        $rules = [
            'product_id' => 'required|integer',
            'qty' => 'required|integer|min:1',
        ];

        $messages = [
            'required' => 'Data harus diisi.',
            'integer' => 'Data harus bernilai angka.',
            'numeric' => 'Data harus bernilai angka.',
            'min' => 'The :attribute field must be at least :min.',
            'string' => 'Data harus bernilai huruf.'
        ];

        $lastOrder = Order::orderBy('id', 'desc')->first();
        $lastOrderCode = $lastOrder ? $lastOrder->invoice : null;
        $lastOrderNumber = $lastOrderCode ? intval(substr($lastOrderCode, 4)) : 0;
        $nextOrderNumber = $lastOrderNumber + 1;
        $newOrder = $nextOrderNumber;


        $validator = Validator::make($rules, $messages);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        $order = Order::create([
            'invoice' => 'INV-' . $newOrder,
            'payment_method' => $request->input('payment_method'),
            'total_price' => calculateTotalAmount($request->input('ordered_items')),
        ]);

        // Store ordered items associated with the Order

        foreach ($request->input('ordered_items') as $orderedItemData) {

            $product = Product::where('id', $orderedItemData['product_id'])->first();

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $orderedItemData['product_id'],
                'qty' => $orderedItemData['qty'],
                'price' => $product->price * $orderedItemData['qty'],
            ]);
        }

        // Return the newly created Order
        if ($order) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Berhasil Membuat Pesanan',
                'data' => $order,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Pesanan gagal ditambahkan",
            ], 400);
        }
    }
}
