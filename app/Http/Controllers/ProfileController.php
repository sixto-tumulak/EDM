<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use ProtoneMedia\Splade\Facades\Toast;
use ProtoneMedia\Splade\Facades\SEO;
use ProtoneMedia\Splade\FileUploads\ExistingFile;
use Illuminate\Support\Facades\Storage; 

use App\Models\User;
use App\Models\Barangay;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    
    /**
     * Display the user's profile form.
     *
     * 
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {

        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }


    /**
     * Display the user's profile form.
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $user = $request->user(); 
        $user_image = $user->profile_picture; 
        // $image = ExistingFile::fromDisk('public')->get('profile_pictures' . $user_image); 
        // $image = ExistingFile::fromDisk('public')->get('public/storage/point-transactions' . '1.jpg'); 
        return view('profile.edit', [
            'user' => $request->user(),
            'address' => Barangay::all(), 
            // 'image' => $image,
        ]);
    }

    /**
     * Update the user's profile information.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileUpdateRequest $request)
    {

        $user = $request->user(); 
         
        $request->validate([
            'profile_picture' => ['required'],
            'cover_photo' => ['required'],
            'bio' => ['max:100'],
            'short_name' => ['required', Rule::unique('users', 'short_name')->ignore($user->id)],
            ],
            [
                'short_name.unique' => 'Short name has already taken. Please try another.'
            ],
        ); 
        
        
        if($request->hasFile('profile_picture')) {
            // $pp_path = Storage::putFile('images', $request->file('profile_picture')); 
            $pp_path = Storage::putFile('public', $request->file('profile_picture'));
        }else {
            $pp_path = null; 
        }
        if($request->hasFile('cover_photo')) { 
            $cp_path = Storage::putFile('public', $request->file('cover_photo')); 
            // $cp_path = $request->file('cover_photo')->store('public/cover_photos');
        }else {
            $cp_path = null; 
        }

        $request->user()->fill($request->validated()); 

        
        $request->user()->fill(
            [
                'name' => $request->name,
                'short_name' => $request->short_name,
                // 'address' => $request->address,
                // 'address' => $user->address,  
                'email' => $request->email,
                'profile_picture' => $pp_path, 
                'cover_photo' => $cp_path, 
                'bio' => $request->bio, 
            ]
        );

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save(); 

        Toast::title('Success!')
        ->message('Your profile has been updated.')
        ->success()
        ->rightTop()
        ->autoDismiss(3);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
