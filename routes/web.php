<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmbedConfigurationController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpaceTestimonialController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('welcome', [
    'plans' => [
        'free' => config('plans.free'),
        'pro' => config('plans.pro'),
    ],
]))->name('home');

Route::get('s/{space:slug}', [CollectionController::class, 'show'])->name('collection.show');
Route::post('s/{space:slug}', [CollectionController::class, 'store'])->middleware('throttle:20,1')->name('collection.store');
Route::get('embed/{publicId}', [EmbedController::class, 'show'])->name('embed.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::resource('spaces', SpaceController::class)->except('show');

    Route::scopeBindings()->group(function () {
        Route::get('spaces/{space}/testimonials', [SpaceTestimonialController::class, 'index'])->name('spaces.testimonials.index');
        Route::patch('spaces/{space}/testimonials/{testimonial}', [SpaceTestimonialController::class, 'update'])->name('spaces.testimonials.update');
        Route::delete('spaces/{space}/testimonials/{testimonial}', [SpaceTestimonialController::class, 'destroy'])->name('spaces.testimonials.destroy');
    });

    Route::get('spaces/{space}/embed', [EmbedConfigurationController::class, 'edit'])->name('spaces.embed.edit');
    Route::put('spaces/{space}/embed', [EmbedConfigurationController::class, 'update'])->name('spaces.embed.update');

    Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
    Route::post('billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('billing/portal', [BillingController::class, 'portal'])->name('billing.portal');
});

require __DIR__.'/settings.php';
