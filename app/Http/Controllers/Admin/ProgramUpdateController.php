<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationProgram;
use App\Models\ProgramUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

use function Pest\Laravel\get;

class ProgramUpdateController extends Controller
{
    /**
     * Menampilkan halaman utama untuk mengelola kabar terbaru.
     * Diawali dengan pemilihan program dari dropdown.
     */
    public function index(Request $request): Response
    {
        $allPrograms = DonationProgram::orderBy('name')->get(['id', 'name', 'slug']);
        $selectedProgramSlug = $request->input('program');
        $updates = null;

        if ($selectedProgramSlug) {
            $updates = ProgramUpdate::whereHas('program', function ($query) use ($selectedProgramSlug) {
                $query->where('slug', $selectedProgramSlug);
            })
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return Inertia::render('Admin/Updates/Index', [
            'allPrograms' => $allPrograms,
            'updates' => $updates,
            'filters' => ['program' => $selectedProgramSlug],
        ]);
    }

    /**
     * Menampilkan form untuk membuat update baru untuk program tertentu.
     */
    public function create(DonationProgram $program): Response
    {
        return Inertia::render('Admin/Updates/Create', [
            'program' => $program,
        ]);
    }

    /**
     * Menyimpan update baru ke database.
     */
    public function store(Request $request, DonationProgram $program)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $program->updates()->create([
            'title' => $request->title,
            'content' => $request->content,
            'published_at' => now(),
        ]);

        // Arahkan kembali ke halaman index update dengan program yang sama terpilih
        return Redirect::route('admin.news.index', ['program' => $program->slug])->with('success', 'Kabar terbaru berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit update yang sudah ada.
     */
    public function edit(ProgramUpdate $update): Response
    {
        $update->load('program:id,name,slug');
        return Inertia::render('Admin/Updates/Edit', [
            'update' => $update,
        ]);
    }

    /**
     * Memperbarui update di database.
     */
    public function update(Request $request, ProgramUpdate $update)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $update->update($request->all());

        return Redirect::route('admin.news.index', ['program' => $update->program->slug])->with('success', 'Kabar terbaru berhasil diperbarui.');
    }

    /**
     * Menghapus update dari database.
     */
    public function destroy(ProgramUpdate $update)
    {
        $programSlug = $update->program->slug;
        $update->delete();

        return Redirect::route('admin.news.index', ['program' => $programSlug])->with('success', 'Kabar terbaru berhasil dihapus.');
    }
}
