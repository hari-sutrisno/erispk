<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Role\Index as RoleIndex;
use App\Livewire\Role\Form as RoleForm;
use App\Livewire\Permission\Index as PermissionIndex;
use App\Livewire\Permission\Form as PermissionForm;
use App\Livewire\User\Index as UserIndex;
use App\Livewire\User\Form as UserForm;

use App\Livewire\Category\Index as CategoryIndex;
use App\Livewire\Category\Form as CategoryForm;

use App\Livewire\Status\Index as StatusIndex;
use App\Livewire\Status\Form as StatusForm;

use App\Livewire\Metode\Index as MetodeIndex;
use App\Livewire\Metode\Form as MetodeForm;

use App\Livewire\Tipe\Index as TipeIndex;
use App\Livewire\Tipe\Form as TipeForm;

Route::redirect('/', '/dashboard');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware('auth')->group(function () {
    Route::middleware(['permission:role index'])->get('/roles', RoleIndex::class)->name('roles.index');
    Route::middleware(['permission:role add'])->get('/roles/create', RoleForm::class)->name('roles.create');
    Route::middleware(['permission:role edit'])->get('/roles/{role}/edit', RoleForm::class)->name('roles.edit');

    Route::middleware(['permission:permission index'])->get('/permissions', PermissionIndex::class)->name('permissions.index');
    Route::middleware(['permission:permission add'])->get('/permissions/create', PermissionForm::class)->name('permissions.create');
    Route::middleware(['permission:permission edit'])->get('/permissions/{permission}/edit', PermissionForm::class)->name('permissions.edit');

    Route::middleware(['permission:user index'])->get('/users', UserIndex::class)->name('users.index');
    Route::middleware(['permission:user add'])->get('/users/create', UserForm::class)->name('users.create');
    Route::middleware(['permission:user edit'])->get('/users/{id}/edit', UserForm::class)->name('users.edit');

    Route::middleware(['permission:category index'])->get('/categories', CategoryIndex::class)->name('categories.index');
    Route::middleware(['permission:category add'])->get('/categories/create', CategoryForm::class)->name('categories.create');
    Route::middleware(['permission:category edit'])->get('/categories/{id}/edit', CategoryForm::class)->name('categories.edit');

    Route::middleware(['permission:status index'])->get('/statuses', StatusIndex::class)->name('statuses.index');
    Route::middleware(['permission:status add'])->get('/statuses/create', StatusForm::class)->name('statuses.create');
    Route::middleware(['permission:status edit'])->get('/statuses/{id}/edit', StatusForm::class)->name('statuses.edit');

    Route::middleware(['permission:metode index'])->get('/metode', MetodeIndex::class)->name('metode.index');
    Route::middleware(['permission:metode add'])->get('/metode/create', MetodeForm::class)->name('metode.create');
    Route::middleware(['permission:metode edit'])->get('/metode/{id}/edit', MetodeForm::class)->name('metode.edit');

    Route::middleware(['permission:tipe index'])->get('/tipe', TipeIndex::class)->name('tipe.index');
    Route::middleware(['permission:tipe add'])->get('/tipe/create', TipeForm::class)->name('tipe.create');
    Route::middleware(['permission:tipe edit'])->get('/tipe/{id}/edit', TipeForm::class)->name('tipe.edit');
});

require __DIR__ . '/auth.php';
