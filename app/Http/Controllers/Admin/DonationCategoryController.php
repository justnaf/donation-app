<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DonationCategoryController extends Controller
{
    public function index(): Response
    {
        // Ambil semua kategori dengan jumlah program di dalamnya
        $categories = DonationCategory::withCount('programs')->latest()->paginate(15);

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:donation_categories',
            'description' => 'nullable|string',
        ]);

        DonationCategory::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        return Redirect::route('admin.donation-categories.index')->with('success', 'Kategori baru berhasil dibuat.');
    }

    public function edit(DonationCategory $donationCategory): Response
    {
        return Inertia::render('Admin/Categories/Edit', [
            'category' => $donationCategory,
        ]);
    }

    public function update(Request $request, DonationCategory $donationCategory)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('donation_categories')->ignore($donationCategory->id)],
            'description' => 'nullable|string',
        ]);

        $donationCategory->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
        ]);

        return Redirect::route('admin.donation-categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(DonationCategory $donationCategory)
    {
        $donationCategory->delete();

        return Redirect::route('admin.donation-categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
