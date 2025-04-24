<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller
{
    /**
     * Show the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')
                ->withErrors($validator)
                ->withInput($request->except('password'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->route('login')
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->withInput($request->except('password'));
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('register')
                ->withErrors($validator)
                ->withInput($request->except('password', 'password_confirmation'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'department' => $request->department,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Account created successfully!');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Display the authenticated user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function showProfile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Show the form for editing the authenticated user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function editProfile()
    {
        $user = Auth::user();
        return view('auth.edit-profile', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'department' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('profile.edit')
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the form for changing the password.
     *
     * @return \Illuminate\View\View
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update the authenticated user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('password.change')
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('password.change')
                ->withErrors(['current_password' => 'The provided password does not match your current password.'])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }

    /**
     * Show the form for account deletion confirmation.
     *
     * @return \Illuminate\View\View
     */
    public function showDeleteAccountForm()
    {
        return view('auth.delete-account');
    }

    /**
     * Delete the user's account.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('account.delete')
                ->withErrors($validator)
                ->withInput();
        }

        $user = Auth::user();

        // Check if the password matches before proceeding with deletion
        if (!Hash::check($request->password, $user->password)) {
            return redirect()->route('account.delete')
                ->withErrors(['password' => 'The provided password is incorrect.'])
                ->withInput();
        }

        // Logout before deleting the account
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Delete the user
        $user->delete();

        return redirect()->route('login')
            ->with('success', 'Your account has been permanently deleted.');
    }
}
