<?php

use App\Http\Controllers\RoleManagementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Authentication Routes (add these to your routes/web.php file)

// Authentication Routes (add these to your routes/web.php file)

// Login Routes
Route::get('/login', [App\Http\Controllers\AuthenticationController::class, 'showLoginForm'])
    ->name('login')
    ->middleware('guest');
Route::post('/login', [App\Http\Controllers\AuthenticationController::class, 'login'])
    ->middleware('guest');

// Registration Routes
Route::get('/register', [App\Http\Controllers\AuthenticationController::class, 'showRegistrationForm'])
    ->name('register')
    ->middleware('guest');
Route::post('/register', [App\Http\Controllers\AuthenticationController::class, 'register'])
    ->middleware('guest');

// Logout Route
Route::post('/logout', [App\Http\Controllers\AuthenticationController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// Profile Routes
Route::middleware('auth')->group(function () {
    // View Profile
    Route::get('/profile', [App\Http\Controllers\AuthenticationController::class, 'showProfile'])
        ->name('profile.show');

    // Edit Profile
    Route::get('/profile/edit', [App\Http\Controllers\AuthenticationController::class, 'editProfile'])
        ->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\AuthenticationController::class, 'updateProfile'])
        ->name('profile.update');

    // Change Password
    Route::get('/password/change', [App\Http\Controllers\AuthenticationController::class, 'showChangePasswordForm'])
        ->name('password.change');
    Route::put('/password', [App\Http\Controllers\AuthenticationController::class, 'updatePassword'])
        ->name('password.update');

    // Delete Account
    Route::get('/account/delete', [App\Http\Controllers\AuthenticationController::class, 'showDeleteAccountForm'])
        ->name('account.delete');
    Route::delete('/account', [App\Http\Controllers\AuthenticationController::class, 'deleteAccount'])
        ->name('account.destroy');
});

Route::middleware('auth')->group(function () {
    // Main dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // User stats section
    Route::get('/dashboard/user-stats', [App\Http\Controllers\DashboardController::class, 'userStats'])
        ->name('dashboard.user-stats');

    // System stats (optionally protected by additional permissions)
    Route::get('/dashboard/system-stats', [App\Http\Controllers\DashboardController::class, 'systemStats'])
        ->name('dashboard.system-stats');
});


Route::middleware(['auth'])->group(function () {
//    Route::get('/admin/roles', [RoleManagementController::class, 'index'])->name('roles.index');
    Route::get('/admin/users/roles', [RoleManagementController::class, 'userRoles'])->name('users.roles');
});

Route::middleware(['auth', 'permission:manage roles'])->group(function () {
    Route::get('/admin/roles', [RoleManagementController::class, 'index'])->name('roles.index');
});
