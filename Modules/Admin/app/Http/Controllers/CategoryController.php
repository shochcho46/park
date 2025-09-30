<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Admin\Entities\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $limit = $request->limit ?? 20;
        $datas = Category::orderBy('id', 'desc')
                    ->when($request->search, function($query) use ($request){
                        $query->where('name', 'like', '%'.$request->search.'%');
                    })
                    ->paginate($limit);

        return view('admin::category.index', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin::category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'status' => 'required|boolean',
        ]);

        Category::create($validatedData);

        $toaster = [
            'message' => 'Category created successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.category.index')->with($toaster);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $data = $category;
        return view('admin::category.edit', compact('data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'status' => 'required|boolean',
        ]);

        $category->update($validatedData);

        $toaster = [
            'message' => 'Category updated successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.category.index')->with($toaster);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        $toaster = [
            'message' => 'Category deleted successfully!',
            'alert-type' => 'success'
        ];

        return redirect()->route('admin.category.index')->with($toaster);
    }
}
