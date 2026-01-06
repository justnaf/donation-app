<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Models\DonationCategory;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DonationProgramController extends Controller
{
    /**
     * Menampilkan daftar semua program donasi untuk admin.
     */
    public function index(Request $request): Response
    {
        $programs = DonationProgram::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10) // <-- PASTIKAN INI ADALAH paginate(), BUKAN get()
            ->withQueryString();

        return Inertia::render('Admin/Programs/Index', [
            'programs' => $programs,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Menampilkan form untuk membuat program donasi baru.
     */
    public function create(): Response
    {
        $categories = DonationCategory::orderBy('name')->get();

        return Inertia::render('Admin/Programs/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Menyimpan program donasi baru ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:donation_programs',
            'donation_category_id' => 'nullable|exists:donation_categories,id',
            'poster' => 'nullable|image|max:2048', // maks 2MB
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'short_description' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => ['required', Rule::in(['draft', 'active'])],
        ]);

        $posterPath = null;
        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('program-posters', 'public');
        }

        DonationProgram::create([
            'name' => $validated['name'],
            'donation_category_id' => $validated['donation_category_id'],
            'slug' => Str::slug($validated['name']) . '-' . uniqid(),
            'poster_path' => $posterPath,
            'target_amount' => $validated['target_amount'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'short_description' => $validated['short_description'],
            'content' => $validated['content'],
            'status' => $validated['status'],
        ]);

        return Redirect::route('admin.programs.index')->with('success', 'Program donasi berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit program donasi.
     */
    public function edit(DonationProgram $program): Response
    {
        $categories = DonationCategory::orderBy('name')->get();

        return Inertia::render('Admin/Programs/Edit', [
            'program' => $program->load('category'), // Eager load kategori yang sudah dipilih
            'categories' => $categories,
        ]);
    }

    /**
     * Memperbarui data program donasi di database.
     */
    public function update(Request $request, DonationProgram $program)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('donation_programs')->ignore($program->id)],
            'poster' => 'nullable|image|max:2048',
            'donation_category_id' => 'nullable|exists:donation_categories,id',
            'target_amount' => 'required|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'short_description' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => ['required', Rule::in(['draft', 'active', 'ended'])],
        ]);

        // Gabungkan data yang divalidasi dengan data lain
        $updateData = $validated;

        // Logika untuk slug
        if ($program->name !== $validated['name']) {
            $updateData['slug'] = Str::slug($validated['name']) . '-' . $program->id;
        }

        // Logika untuk upload poster baru
        if ($request->hasFile('poster')) {
            // Hapus poster lama jika ada
            if ($program->poster_path) {
                Storage::disk('public')->delete($program->poster_path);
            }
            // Simpan poster baru dan update path-nya
            $updateData['poster_path'] = $request->file('poster')->store('program-posters', 'public');
        }

        $program->update($updateData);

        return Redirect::route('admin.programs.index')->with('success', 'Program donasi berhasil diperbarui.');
    }

    /**
     * Menghapus program donasi (soft delete).
     */
    public function destroy(DonationProgram $program)
    {
        // Menggunakan forceDelete() akan menghapus data permanen dari database
        // meskipun model menggunakan trait SoftDeletes.
        $program->forceDelete();

        return Redirect::route('admin.programs.index')
            ->with('success', 'Program donasi berhasil dihapus secara permanen.');
    }
    public function updateStatus(Request $request, DonationProgram $program)
    {
        $request->validate(['status' => ['required', Rule::in(['draft', 'active', 'ended'])]]);
        $program->update(['status' => $request->status]);
        return back()->with('success', 'Status program berhasil diubah.');
    }
}
