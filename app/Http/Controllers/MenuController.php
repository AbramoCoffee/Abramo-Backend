<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::paginate(10);
        return view('pages.menu.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = DB::table('categories')->get();
        return view('pages.menu.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validate the request...
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'qty' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // store the request...
        $menu = new Menu;
        $menu->code = $request->code;
        $menu->name = $request->name;
        $menu->description = $request->description;
        $menu->price = $request->price;
        $menu->category_id = $request->category_id;
        $menu->qty = $request->qty;
        $menu->status = $request->status;
        $menu->is_favorite = $request->is_favorite;

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/menus', date("YmdHis") . '.' . $image->getClientOriginalExtension());
            $menu->image = 'storage/menus/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            $menu->save();
        } else {
            $menu->save();
        }

        return redirect()->route('menu.index')->with('success', 'Menu created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('pages.menu.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $menu = Menu::findOrFail($id);
        $categories = DB::table('categories')->get();
        return view('pages.menu.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // validate the request...
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'qty' => 'required|numeric',
            'status' => 'required|boolean',
            'is_favorite' => 'required|boolean',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // update the request...
        $menu = menu::find($id);
        $menu->code = $request->code;
        $menu->name = $request->name;
        $menu->description = $request->description;
        $menu->price = $request->price;
        $menu->category_id = $request->category_id;
        $menu->qty = $request->qty;
        $menu->status = $request->status;
        $menu->is_favorite = $request->is_favorite;
        $menu->save();

        //save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $image->storeAs('public/menus', date("YmdHis") . '.' . $image->getClientOriginalExtension());
            $menu->image = 'storage/menus/' . date("YmdHis") . '.' . $image->getClientOriginalExtension();
            $menu->save();
        }

        return redirect()->route('menu.index')->with('success', 'Menu updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // delete the request...
        $menu = Menu::find($id);
        $menu->delete();

        return redirect()->route('menu.index')->with('success', 'Menu deleted successfully');
    }
}
