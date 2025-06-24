<?php

namespace App\Http\Controllers;

use App\Models\DonationProgram;
use App\Models\DonationCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Inertia\Inertia;

class DonationProgramController extends Controller
{
    public function index(): Response
    {
        $programs = DonationProgram::where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>', now());
            })
            ->latest()
            ->paginate(6); // Kita buat 6 per halaman agar pas di grid

        return Inertia::render('Public/Home', [ // Kita akan render ke view Home baru
            'programs' => $programs,
        ]);
    }
    public function indexPrograms(Request $request): Response
    {
        // Ambil semua kategori yang memiliki setidaknya satu program aktif
        $categories = DonationCategory::whereHas('programs', function ($query) {
            $query->where('status', 'active');
        })->get();

        $programs = DonationProgram::with('category') // Eager load relasi kategori
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('end_date')->orWhere('end_date', '>', now());
            })
            // Filter berdasarkan kategori slug jika ada di request
            ->when($request->input('category'), function ($query, $categorySlug) {
                $query->whereHas('category', function ($q) use ($categorySlug) {
                    $q->where('slug', $categorySlug);
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return Inertia::render('Public/Programs/Index', [
            'programs' => $programs,
            'categories' => $categories,
            'filters' => $request->only(['category']),
        ]);
    }

    public function showProgram(DonationProgram $program): Response
    {
        $user = Auth::user();
        if ($user instanceof User) {
            if ($program->status !== 'active' && $user->hasRole('donatur')) {
                abort(404);
            }
        } else {
            if ($program->status !== 'active') {
                abort(404);
            }
        }
        $program->load([
            'category',
            'donations' => function ($query) {
                $query->where('status', 'paid')->latest()->take(10);
            },
            'updates' => function ($query) {
                $query->latest();
            },
            'disbursements' => function ($query) {
                $query->latest();
            }
        ]);

        return Inertia::render('Public/Programs/Show', [
            'program' => $program,
            'fees' => config('fees.midtrans'), // <-- TAMBAHKAN INI
        ]);
    }
}
