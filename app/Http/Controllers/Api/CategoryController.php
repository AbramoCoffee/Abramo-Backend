<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

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
        //validate the request...
        $request->validate([
            'name' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //store the request...
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/categories', date("YmdHis") . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            $category->save();
        } else {
            $category->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kategori berhasil ditambahkan',
            'data' => $category
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $category = Category::find($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Data category ditemukan',
            'data' => $category,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        //validate the request...
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        //update the request...
        $category = Category::find($id);
        $category->name = $request->name;
        $category->description = $request->description;

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
            $category->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Data category berhasil diperbarui',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //delete the request...
        $category = Category::find($id);
        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data category berhasil dihapus',
            'data' => $category,
        ], 200);
    }
}
