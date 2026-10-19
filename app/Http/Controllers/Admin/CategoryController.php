<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => ServiceCategory::withCount('requests')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        ServiceCategory::create($this->validated($request));

        return back()->with('success', 'Service category added.');
    }

    public function edit(ServiceCategory $category)
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function update(Request $request, ServiceCategory $category): RedirectResponse
    {
        $category->update($this->validated($request, $category));

        return redirect()->route('admin.categories.index')->with('success', 'Service category updated.');
    }

    public function destroy(ServiceCategory $category): RedirectResponse
    {
        if ($category->requests()->exists()) {
            return back()->with('error', 'This service already has requests. Deactivate it instead of deleting it.');
        }

        $category->delete();

        return back()->with('success', 'Service category deleted.');
    }

    private function validated(Request $request, ?ServiceCategory $category = null): array
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:100', Rule::unique('service_categories', 'name')->ignore($category)],
            'office'      => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
