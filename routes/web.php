<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ManagePropertyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\UserManagementController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', HomeController::class)->name('home');
Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
Route::get('/compare', fn () => Inertia::render('Compare'))->name('compare');
Route::get('/about', fn () => Inertia::render('Static/About'))->name('about');
Route::get('/contacts', fn () => Inertia::render('Static/Contacts'))->name('contacts');
Route::get('/realtors', fn () => Inertia::render('Static/Realtors', ['realtors' => User::where('role', 'realtor')->where('is_blocked', false)->withCount('properties')->get()]))->name('realtors');
Route::post('/contacts', [ContactRequestController::class, 'store'])->name('contacts.store');
Route::post('/applications/{property?}', [ApplicationController::class, 'store'])->name('applications.store');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{property}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{property}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::get('/my-applications', [ApplicationController::class, 'index'])->name('applications.index');
});

Route::middleware(['auth', 'role:realtor,admin'])->prefix('manage')->name('manage.')->group(function () {
    Route::post('/properties/{property}', [ManagePropertyController::class, 'update'])->name('properties.update.post');
    Route::resource('properties', ManagePropertyController::class)->except('show');
    Route::delete('/properties/{property}/images/{image}', [ManagePropertyController::class, 'destroyImage'])->name('properties.images.destroy');
    Route::get('/applications', [ApplicationController::class, 'index'])->name('applications.index');
    Route::patch('/applications/{application}', [ApplicationController::class, 'update'])->name('applications.update');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::get('/references', [ReferenceController::class, 'index'])->name('references.index');
    Route::post('/property-types', [ReferenceController::class, 'storeType'])->name('property-types.store');
    Route::delete('/property-types/{propertyType}', [ReferenceController::class, 'destroyType'])->name('property-types.destroy');
    Route::post('/amenities', [ReferenceController::class, 'storeAmenity'])->name('amenities.store');
    Route::delete('/amenities/{amenity}', [ReferenceController::class, 'destroyAmenity'])->name('amenities.destroy');
});

require __DIR__.'/auth.php';
