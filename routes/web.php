<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\RoleManagementController;
use App\Livewire\Integration\CreateRequest;
use App\Livewire\Integration\IntegrationsList;
use App\Livewire\Integration\MyRequests;
use App\Livewire\Integration\PendingApprovals;
use App\Livewire\Integration\ShowRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return redirect('/login');
});
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
    Route::get('/admin/users/roles', [RoleManagementController::class, 'userRoles'])->name('users.roles');
});

Route::middleware(['auth', 'permission:manage roles'])->group(function () {
    Route::get('/admin/roles', [RoleManagementController::class, 'index'])->name('roles.index');
});


Route::middleware('auth')->group(function () {
    // All integrations
    Route::get('/integrations', IntegrationsList::class)->name('integrations.index');

    // Create new integration request - Integration type selection screen
    Route::get('/integrations/create', function() {
        return view('integration.select');
    })->name('integrations.create');


    Route::get('integrations/{integration}', function(App\Models\Integration $integration) {
        return view('livewire.integration.show', ['integration' => $integration]);
    })->name('integrations.show');

    // My integration requests
    Route::get('/my-integrations', MyRequests::class)->name('integrations.my');

    // Pending approvals
    Route::get('/pending-approvals', PendingApprovals::class)->name('integrations.pending');

    // Integration form routes
    Route::get('integrations/internal/create', function() {
        return view('livewire.integration.internal');
    })->name('integrations.internal.create');

    Route::get('integrations/external/create', function() {
        return view('livewire.integration.external');
    })->name('integrations.external.create');
});

Route::middleware(['auth', 'role:administrator'])->prefix('admin')->name('admin.')->group(function () {
    // User Management
    Route::get('/users', function () {
        return view('admin.users.index');
    })->name('users.index');
    // Configuration Management
    Route::get('/configurations', function () {
        return view('admin.configurations.index');
    })->name('configurations.index')->middleware('permission:manage configuration');

    Route::get('/vendors', function () {
        return view('admin.vendors.index');
    })->name('vendors.index')->middleware('permission:view vendor');
    Route::get('/sla-configurations', function () {
        return view('admin.sla-configurations.index');
    })->name('sla-configurations.index')
        ->middleware('permission:manage configuration');

});

Route::get('download-attachment/{id}', [AttachmentController::class, 'download'])
    ->name('attachments.download')
    ->middleware('auth');
