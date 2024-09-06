<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // index
    public function index(Request $request)
    {
        $categories = Category::when($request->input('name'), function($query, $name) {
            $query->where('name', 'like', '%'.$name.'%');
        })->paginate(10);

        return view('pages.categories.index', compact('categories'));
    }

    // create
    public function create()
    {
        return view('pages.categories.create');
    }

    // store
    public function store(Request $request)
    {
        // Validate the request...
        $request->validate([
            'name' => 'required',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Store the request...
        $category = new Category;
        $category->name = $request->name;
        $category->description = $request->description;
        $category->save();

        // Save image
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
            $category->save();
        }

        return redirect()->route('categories.index')->with('success', 'Category created successfully');
    }

    // show
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.categories.show', compact('category'));
    }

    // edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('pages.categories.edit', compact('category'));
    }

    // update
    public function update(Request $request, $id)
    {
        // Validate the request...
        $request->validate([
            'name' => 'required',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Update the request...
        $category = Category::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;

        // Save image if provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image && Storage::exists(str_replace('storage/', 'public/', $category->image))) {
                Storage::delete(str_replace('storage/', 'public/', $category->image));
            }

            $image = $request->file('image');
            $imagePath = $image->storeAs('public/categories', $category->id . '.' . $image->getClientOriginalExtension());
            $category->image = 'storage/categories/' . $category->id . '.' . $image->getClientOriginalExtension();
        }

        $category->save();

        return redirect()->route('categories.index')->with('success', 'Category updated successfully');
    }

    // destroy
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Delete image from storage if it exists
        if ($category->image && Storage::exists(str_replace('storage/', 'public/', $category->image))) {
            Storage::delete(str_replace('storage/', 'public/', $category->image));
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Category deleted successfully');
    }
}
