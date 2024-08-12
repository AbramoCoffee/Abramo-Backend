<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function getOrders()
    {
        $orders = Order::orderBy('id', 'DESC')->get();

        foreach ($orders as $key => $r) {
            $orders[$key]->data_items = OrderItem::orderBy('id', 'DESC')->get()->load('menu')->load('order');
        }

        // foreach ($orders as $key => $r) {
        //     $orders[$key]->data_items = OrderItem::join('menus', 'order_items.menu_id', '=', 'menus.id')->select('order_items.*', 'menus.name')->where('order_items.order_id', $r->id)->get();
        // }

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

        $income = $query->sum('total_price');
        $orders = $query->orderBy('id', 'DESC')->get();

        foreach ($orders as $key => $r) {
            $orders[$key]->data_items = OrderItem::orderBy('id', 'DESC')->get()->load('menu')->load('order');
        }

        // foreach ($orders as $key => $r) {
        //     $orders[$key]->data_items = DB::table('order_items')->join('menus', 'order_items.menu_id', '=', 'menus.id')->select('order_items.*', 'menus.name')->where('order_items.order_id', $r->id)->get();
        // }

        if ($orders) {
            return response()->json(
                [
                    'status' => 'Success',
                    'message' => 'List Data Order',
                    'data' => $orders,
                    'income' => $income
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

                $menu = Menu::where('id', "=", $orderedItem['menu_id'])->first();

                $totalAmount += $menu->price * $orderedItem['qty'];
            }

            return $totalAmount;
        }

        $rules = [
            'menu_id' => 'required|integer',
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

        $user = User::where('id', $request->input('cashier'))->first();

        $create_order = Order::create([
            'invoice' => 'INV-' . $newOrder,
            'konsumen' => $request->input('konsumen'),
            'cashier' => $request->input('cashier'),
            'payment_method' => $request->input('payment_method'),
            'total_price' => calculateTotalAmount($request->input('ordered_items')),
            'total_paid' => $request->input('total_paid'),
            'total_return' => $request->input('total_paid') - calculateTotalAmount($request->input('ordered_items')),
            'status' => $request->input('status'),
        ]);

        $order = [
            'invoice' => 'INV-' . $newOrder,
            'konsumen' => $request->input('konsumen'),
            'payment_method' => $request->input('payment_method'),
            'total_price' => calculateTotalAmount($request->input('ordered_items')),
            'total_paid' => $request->input('total_paid'),
            'total_return' => $request->input('total_paid') - calculateTotalAmount($request->input('ordered_items')),
            'cashier' => $user,
            'status' => $request->input('status'),
        ];

        // Store ordered items associated with the Order

        foreach ($request->input('ordered_items') as $orderedItemData) {

            $menu = Menu::where('id', $orderedItemData['menu_id'])->first();

            $menu->qty -= $orderedItemData['qty'];
            $menu->save();

            OrderItem::create([
                'order_id' => $create_order->id,
                'menu_id' => $orderedItemData['menu_id'],
                'qty' => $orderedItemData['qty'],
                'price' => $menu->price * $orderedItemData['qty'],
            ]);
        }

        // Return the newly created Order
        if ($create_order) {
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


    public function updateOrder(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'invoice' => 'required',
            'konsumen' => 'required',
            'cashier' => 'required',
            'payment_method' => 'required',
            'total_price' => 'required',
            'total_paid' => 'required',
            'total_return' => 'required',
            'status' => 'required',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $orders = Order::find($id);

        $data = [
            'invoice' => $request->input('invoice'),
            'konsumen' => $request->input('konsumen'),
            'cashier' => $request->input('cashier'),
            'payment_method' => $request->input('payment_method'),
            'total_price' => $request->input('total_price'),
            'total_paid' => $request->input('total_paid'),
            'total_return' => $request->input('total_return'),
            'status' => $request->input('status'),
        ];


        $orders->update($data);

        if ($orders) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Orders berhasil diupdate',
                'data' => $orders,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Orders gagal diupdate',
            ], 400);
        }
    }
}
