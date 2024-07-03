<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
        // get all categories
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'name' => $request->name,
        ];

        if ($request->description != null) {
            $data['description'] = $request->description;
        }

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/categories', date("YmdHis") . '.' . $image->getClientOriginalExtension());
            $data['image'] = 'storage/categories/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
        }

        //create category
        $category = Category::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Kategori berhasil ditambahkan',
                'data' => $category,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => "Kategori gagal ditambahkan",
            ], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data kategori ditemukan',
            'data' => $category,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $category = Category::find($id);

        $data = [
            'name' => $request->name,
        ];

        if ($request->description != null) {
            $data['description'] = $request->description;
        }

        if ($request->hasFile('image')) {
            if ($category->image == null) {
                $image = $request->file('image');
                $image->storeAs('public/categories', date("YmdHis") . '.' . $image->getClientOriginalExtension());
                $data['image'] = 'storage/categories/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            } else {
                Storage::delete(Category::find($id)->image);
                $image = $request->file('image');
                $image->storeAs('public/categories', date("YmdHis") . '.' . $image->getClientOriginalExtension());
                $data['image'] = 'storage/categories/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            }
        }

        $category->update($data);

        if ($category) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Kategori berhasil diupdate',
                'data' => $category,
            ]);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Kategori gagal diupdate',
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        if (Category::find($id)->image != NULL) {
            Storage::delete(Category::find($id)->image);
        }

        $category = Category::find($id);

        Category::find($id)->delete();

        if ($category) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Kategori berhasil dihapus',
                'data' => $category,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Kategori gagal dihapus',
            ], 400);
        }
    }
}
