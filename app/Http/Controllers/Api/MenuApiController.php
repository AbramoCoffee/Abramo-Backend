<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Menu;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuApiController extends Controller
{


    public function index()
    {
        //get all menu
        $menus = Menu::orderBy('id', 'DESC')->get();
        $menus->load('category');
        return response()->json([
            'status' => 'success',
            'message' => 'List menu ditemukan',
            'data' => $menus
        ], 200);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');

        if ($query) {
            $menus = Menu::where('name', 'like', "%$query%")
                ->orWhere('description', 'like', "%$query%")
                ->get();
        } else {
            $menus = Menu::all();
        }

        $menus->load('category');
        return response()->json([
            'status' => 'success',
            'message' => 'List menu ditemukan',
            'data' => $menus
        ], 200);
    }

    // Filter menu items by category ID
    public function filterByCategory(Request $request, $categoryId)
    {
        $menus = Menu::where('category_id', $categoryId)->get();
        return response()->json([
            'status' => 'success',
            'message' => 'List menu ditemukan',
            'data' => $menus
        ], 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'qty' => 'required|numeric',
            'status' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);


        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $data = [
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'qty' => $request->qty,
            'status' => $request->status,
        ];

        // upload image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/menus', date("YmdHis") . '.' . $image->getClientOriginalExtension());
            $data['image'] = 'storage/menus/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
        }

        //create menu
        $menu = Menu::create($data);

        //return response
        if ($data) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Menu berhasil ditambahkan',
                'data' => $menu,
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
        $menu = Menu::find($id);
        $menu->load('category');
        return response()->json([
            'status' => 'success',
            'message' => 'Data menu ditemukan',
            'data' => $menu,
        ], 200);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'qty' => 'required|numeric',
            'status' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $menu = Menu::find($id);

        // var_dump($request->qty);

        $data = [
            'code' => $request->code,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'qty' => $request->qty,
            'status' => $request->status,
        ];

        if ($request->hasFile('image')) {
            if ($menu->image == null) {
                $image = $request->file('image');
                $image->storeAs('public/menus', date("YmdHis") . '.' . $image->getClientOriginalExtension());
                $data['image'] = 'storage/menus/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            } else {
                Storage::delete(Menu::find($id)->image);
                $image = $request->file('image');
                $image->storeAs('public/menus', date("YmdHis") . '.' . $image->getClientOriginalExtension());
                $data['image'] = 'storage/menus/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            }
        }

        $menu->update($data);

        if ($menu) {
            return response()->json([
                'status' => 'Success',
                'message' => "Menu berhasil di edit",
                'data' => $menu,
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
        if (Menu::find($id)->image != NULL) {
            Storage::delete(Menu::find($id)->image);
        }

        $menu = Menu::find($id);

        Menu::find($id)->delete();

        if ($menu) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Menu berhasil dihapus',
                'data' => $menu,
            ], 200);
        } else {
            return response()->json([
                'status' => 'Failed',
                'message' => 'Menu gagal dihapus',
            ], 400);
        }
    }
}
