<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the landlord's profile form.
     */
    public function edit(Request $request): View
    {
        return view('landlord.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the landlord's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Store new photo
            $photo = $request->file('profile_photo');
            $filename = 'profile_photos/' . Str::uuid() . '.' . $photo->getClientOriginalExtension();
            $path = $photo->storeAs('profile_photos', basename($filename), 'public');
            $validated['profile_photo_path'] = $path;
        }
        
        // Remove profile_photo from validated data as it's handled above
        unset($validated['profile_photo']);
        
        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('landlord.profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the landlord's profile photo.
     */
    public function deletePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();
        
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();
        }
        
        return Redirect::route('landlord.profile.edit')->with('status', 'photo-deleted');
    }
}
