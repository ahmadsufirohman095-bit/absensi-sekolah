<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // --- Implementasi Rate Limiting per-Email ---

        // Membuat kunci unik untuk rate limiter berdasarkan email
        $throttleKey = 'reset-password|' . $request->input('email');

        // Jika terlalu banyak percobaan (lebih dari 3 kali per menit)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            // Membuat pesan error secara manual dengan waktu tunggu
            $message = "Terlalu banyak percobaan. Silakan coba lagi dalam {$seconds} detik.";

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => $message]);
        }

        // Menandai bahwa satu percobaan telah dilakukan
        RateLimiter::hit($throttleKey, 60); // 60 detik masa tunggu

        // --- Akhir dari Rate Limiting ---

        // Mengirim link reset password
        $status = Password::sendResetLink($request->only('email'));

        // Memberikan respons berdasarkan status pengiriman link
        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
