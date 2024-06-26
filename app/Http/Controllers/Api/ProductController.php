<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        //get all products
        $products = Product::all();
        // $products = Product::paginate(10);
        return response()->json([
            'status' => 'success',
            'data' => $products
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock,
            'status' => $request->status,
            'is_favorite' => $request->is_favorite,
        ];

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/products', date("YmdHis") . '.' . $image->getClientOriginalExtension());
            $data['image'] = 'storage/products/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
        }

        //create product
        $product = Product::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Menu berhasil ditambahkan',
                'data' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Menu gagal ditambahkan",
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data menu ditemukan',
            'data' => $product,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'stock' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $product = Product::find($id);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'stock' => $request->stock,
            'status' => $request->status,
            'is_favorite' => $request->is_favorite,
        ];

        if ($request->hasFile('image')) {
            if ($product->image == null) {
                $image = $request->file('image');
                $image->storeAs('public/products', date("YmdHis") . '.' . $image->getClientOriginalExtension());
                $data['image'] = 'storage/products/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            } else {
                Storage::delete(Product::find($id)->image);
                $image = $request->file('image');
                $image->storeAs('public/products', date("YmdHis") . '.' . $image->getClientOriginalExtension());
                $data['image'] = 'storage/products/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            }
        }

        $product->update($data);

        if ($product) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Menu berhasil diupdate',
                'data' => $product,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Menu gagal diupdate',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Product::find($id)->image != NULL) {
            Storage::delete(Product::find($id)->image);
        }

        $product = Product::find($id);

        Product::find($id)->delete();

        if ($product) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Menu berhasil dihapus',
                'data' => $product,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Menu gagal dihapus',
            ], 400);
        }
    }
}
