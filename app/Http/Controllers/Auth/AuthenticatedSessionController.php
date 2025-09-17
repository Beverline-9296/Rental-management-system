<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Get the authenticated user
        $user = Auth::user();
        
        // Check if user is a tenant and not verified
        if ($user && $user->isTenant() && !$user->is_verified) {
            // Check if verification code has expired
            if ($user->verification_code_expires_at && now()->isAfter($user->verification_code_expires_at)) {
                // Generate new verification code
                $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                $user->update([
                    'verification_code' => $verificationCode,
                    'verification_code_expires_at' => now()->addHours(24),
                ]);
                
                // Optionally send new verification email here
                \Log::info('Verification code regenerated for expired code', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
            }
            
            return redirect()->route('account.verification.show')
                ->with('info', 'Please verify your account to continue. Check your email for the verification code.');
        }

        // Log the login activity for both tenants and landlords
        if ($user && ($user->isTenant() || $user->isLandlord())) {
            ActivityLog::logActivity(
                $user->id,
                'login',
                'Logged into the system',
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'role' => $user->role
                ],
                'fas fa-sign-in-alt',
                'blue'
            );
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
