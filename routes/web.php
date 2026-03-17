<?php

use App\Livewire\Cart;
use Livewire\Volt\Volt;
use App\Livewire\Barcode;
use App\Livewire\History;
use App\Livewire\Checkout;
use Laravel\Fortify\Features;
use App\Livewire\Items\ItemEdit;
use App\Livewire\Items\ItemIndex;
use App\Livewire\Items\ItemCreate;
use App\Livewire\Items\OutOfStock;
use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;

Route::any('/register', function () {
    abort(404);
});

Route::any('/forgot-password', function () {
    abort(404);
});

Route::any('/reset-password', function () {
    abort(404);
});

Route::get('/', function () {
    return redirect()->route('dashboard');
})->name('home');

Route::get('/dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    // Route::resource('items', ItemController::class);
    Route::get('/items', ItemIndex::class)->name('items.index');
    Route::get('/items/create', ItemCreate::class)->name('items.create');
    Route::get('/items/{item}/edit', ItemEdit::class)->name('items.edit');
    Route::get('/items/out-of-stock', OutOfStock::class)->name('items.out-of-stock');
    Route::get('/items/{id}/checkout', Checkout::class)->name('items.checkout');
    Route::get('/history', History::class)->name('history.index');
    Route::get('/cart', Cart::class)->name('cart.index');
    Route::get('/barcode', Barcode::class)->name('barcode.index');
});
