<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerCategory;



class CustomerCategoryController extends Controller
{

    public function index()
    {
        if (\Auth::user()->type == 'super admin') {
            $categories = CustomerCategory::get();

            return view('customer_category.index', compact('categories'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function create()
    {
        return view('customer_category.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'extra_field' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $category = new CustomerCategory();
        $category->name = $request->name;
        $category->description = $request->description;
        $category->extra_field = $request->extra_field;
        $category->is_active = $request->is_active;
        $category->save();

        return redirect()->route('customer-category.index')->with('success', __('Customer category created successfully.'));
    }


    public function edit($id)
    {
        $category = CustomerCategory::findOrFail($id);

        return view('customer_category.edit', compact('category'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'nullable',
            'extra_field' => 'nullable',
            'is_active' => 'boolean',
        ]);

        $category = CustomerCategory::findOrFail($id);
        $category->name = $request->name;
        $category->description = $request->description;
        $category->extra_field = $request->extra_field;
        $category->is_active = $request->is_active;
        $category->save();

        return redirect()->route('customer-category.index')->with('success', __('Customer category updated successfully.'));
    }


    public function destroy($id)
    {
        $category = CustomerCategory::findOrFail($id);

        $category->delete();

        return redirect()->route('customer-category.index')->with('success', __('Customer category deleted successfully.'));
    }
}
