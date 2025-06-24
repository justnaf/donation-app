<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonationProgram;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class DonationController extends Controller
{
    /**
     * Menampilkan daftar donasi untuk sebuah program spesifik.
     */
    public function index(Request $request, DonationProgram $program): Response
    {
        $donations = $program->donations()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('donator_name', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%");
            })
            ->when($request->input('status'), function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Donations/Index', [
            'program' => $program,
            'donations' => $donations,
            'filters' => $request->only(['search', 'status']),
        ]);
    }
    public function store(Request $request, DonationProgram $program)
    {
        $request->validate([
            'donator_name' => 'required|string|max:255',
            'donator_email' => 'required|email|max:255',
            'amount' => 'required|numeric|min:1000',
            'donation_date' => 'required|date',
            'is_anonymous' => 'nullable|boolean',
            'message' => 'nullable|string',
        ]);

        // Buat donasi baru, langsung dengan status 'paid'
        $program->donations()->create([
            'order_id' => 'CASH-' . uniqid(), // Prefix 'CASH-' untuk membedakan
            'donator_name' => $request->donator_name,
            'donator_email' => $request->donator_email,
            'amount' => $request->amount,
            'message' => $request->message,
            'is_anonymous' => $request->is_anonymous ?? false,
            'status' => 'paid', // <-- Langsung 'paid'
            'payment_method' => 'cash/offline', // Tandai sebagai donasi tunai/offline
            // Gunakan tanggal dari input admin untuk akurasi pencatatan
            'created_at' => $request->donation_date,
            'updated_at' => $request->donation_date,
        ]);

        return back()->with('success', 'Donasi tunai berhasil dicatat.');
    }
}
