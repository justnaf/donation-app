<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Show the password reset link request page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('auth/ForgotPassword', [
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);


        $email = strtolower($request->input('email'));

        $baseKey = 'password-reset:' . $email;
        $attemptKey = $baseKey . ':attempts';
        $lockKey = $baseKey . ':lock';

        $attempts = Cache::get($attemptKey, 0);

        $delays = [
            0 => 15,             // Percobaan ke-1 -> cooldown 15 dtk
            1 => 60,             // Percobaan ke-2 -> cooldown 1 mnt
            2 => 5 * 60,         // Percobaan ke-3 -> cooldown 5 mnt
            3 => 15 * 60,        // Percobaan ke-4 -> cooldown 15 mnt
            4 => 60 * 60,        // Percobaan ke-5 -> cooldown 1 jam
            5 => 24 * 60 * 60,   // Percobaan ke-6+ -> cooldown 24 jam
        ];
        $delay = $delays[min($attempts, 5)];

        if (Cache::has($lockKey)) {
            throw ValidationException::withMessages([
                'email' => trans('auth.throttle', [
                    'seconds' => Cache::get($lockKey . ':ttl', $delay),
                    'minutes' => ceil(Cache::get($lockKey . ':ttl', $delay) / 60),
                ]),
            ]);
        }

        Password::sendResetLink($request->only('email'));

        Cache::put($lockKey, true, $delay);
        Cache::put($lockKey . ':ttl', $delay, $delay);

        Cache::put($attemptKey, $attempts + 1, now()->addHours(24));

        return back()->with('status', __('A reset link will be sent if the account exists.'));
    }
}
