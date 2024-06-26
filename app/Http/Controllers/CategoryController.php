<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return view('pages.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
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

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return view('pages.categories.show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $category = Category::find($id);
        return view('pages.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //validate the request...
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
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

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //delete the request...
        $category = Category::find($id);
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
