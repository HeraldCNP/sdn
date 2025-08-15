<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

use App\Http\Controllers\SanctionsQueryController;

Route::get('/', [SanctionsQueryController::class, 'dashboard'])->name('dashboard');

Route::prefix('sanctions')->name('sanctions.')->group(function () {
    Route::get('/', [SanctionsQueryController::class, 'dashboard'])->name('dashboard');
    Route::get('/search-profile', [SanctionsQueryController::class, 'searchProfile'])->name('search-profile');
    Route::get('/network-analysis', [SanctionsQueryController::class, 'networkAnalysis'])->name('network-analysis');
    Route::get('/entity-analysis', [SanctionsQueryController::class, 'entityAnalysis'])->name('entity-analysis');
    Route::get('/name-analysis', [SanctionsQueryController::class, 'nameAnalysis'])->name('name-analysis');
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__.'/auth.php';
