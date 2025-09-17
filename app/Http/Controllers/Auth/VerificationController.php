<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class VerificationController extends Controller
{
    /**
     * Show the verification form
     */
    public function show()
    {
        $user = Auth::user();
        
        // Redirect if user is already verified
        if ($user->is_verified) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.verify-account');
    }
    
    /**
     * Verify the user's account with the verification code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6',
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        $user = Auth::user();
        
        // Check if verification code is valid and not expired
        if (!$user->verification_code || 
            $user->verification_code !== $request->verification_code ||
            now()->isAfter($user->verification_code_expires_at)) {
            
            return back()->withErrors([
                'verification_code' => 'Invalid or expired verification code.'
            ]);
        }
        
        // Update user as verified and set new password
        $user->update([
            'password' => Hash::make($request->new_password),
            'is_verified' => true,
            'first_login_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);
        
        Log::info('User account verified successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role
        ]);
        
        return redirect()->route('dashboard')
            ->with('success', 'Your account has been verified successfully! Welcome to the rental management system.');
    }
    
    /**
     * Resend verification code
     */
    public function resend(Request $request)
    {
        $user = Auth::user();
        
        // Generate new verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addHours(24),
        ]);
        
        // Send new verification email (you can create a separate mail class for this)
        // Mail::to($user->email)->send(new VerificationCodeResend($user, $verificationCode));
        
        Log::info('Verification code resent', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);
        
        return back()->with('success', 'A new verification code has been sent to your email.');
    }
}
