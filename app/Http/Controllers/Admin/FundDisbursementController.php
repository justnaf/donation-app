<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationProgram;
use App\Models\FundDisbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class FundDisbursementController extends Controller
{
    /**
     * Menampilkan halaman utama untuk mengelola pencairan dana.
     * Diawali dengan pemilihan program dari dropdown.
     */
    public function index(Request $request): Response
    {
        // Ambil semua program (hanya id, name, slug) untuk mengisi dropdown
        $allPrograms = DonationProgram::orderBy('name')->get(['id', 'name', 'slug']);

        // Ambil slug program yang sedang dipilih dari query string URL
        $selectedProgramSlug = $request->input('program');
        $disbursements = null;

        if ($selectedProgramSlug) {
            // Jika ada program yang dipilih, ambil data pencairan dananya dengan paginasi
            $disbursements = FundDisbursement::whereHas('program', function ($query) use ($selectedProgramSlug) {
                $query->where('slug', $selectedProgramSlug);
            })
                ->latest('disbursed_at') // Urutkan dari tanggal pencairan terbaru
                ->paginate(15)
                ->withQueryString();
        }

        return Inertia::render('Admin/Disbursements/Index', [
            'allPrograms' => $allPrograms,
            'disbursements' => $disbursements,
            'filters' => ['program' => $selectedProgramSlug],
        ]);
    }

    /**
     * Menampilkan form untuk mencatat pencairan dana baru.
     */
    public function create(DonationProgram $program): Response
    {
        return Inertia::render('Admin/Disbursements/Create', [
            'program' => $program,
        ]);
    }

    /**
     * Menyimpan data pencairan dana baru.
     */
    public function store(Request $request, DonationProgram $program)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'disbursed_at' => 'required|date',
        ]);

        $program->disbursements()->create($request->all());

        // Arahkan kembali ke halaman index dengan program yang sama terpilih
        return Redirect::route('admin.disbursements.index', ['program' => $program->slug])->with('success', 'Pencatatan pencairan dana berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit data pencairan dana.
     */
    public function edit(FundDisbursement $disbursement): Response
    {
        // Muat relasi program agar bisa ditampilkan di form/breadcrumbs
        $disbursement->load('program:id,name,slug');
        return Inertia::render('Admin/Disbursements/Edit', [
            'disbursement' => $disbursement,
        ]);
    }

    /**
     * Memperbarui data pencairan dana.
     */
    public function update(Request $request, FundDisbursement $disbursement)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'disbursed_at' => 'required|date',
        ]);

        $disbursement->update($request->all());

        return Redirect::route('admin.disbursements.index', ['program' => $disbursement->program->slug])->with('success', 'Data pencairan dana berhasil diperbarui.');
    }

    /**
     * Menghapus data pencairan dana.
     */
    public function destroy(FundDisbursement $disbursement)
    {
        $programSlug = $disbursement->program->slug;
        $disbursement->delete();

        return Redirect::route('admin.disbursements.index', ['program' => $programSlug])->with('success', 'Data pencairan dana berhasil dihapus.');
    }
}
