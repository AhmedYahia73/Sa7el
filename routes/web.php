<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Dedoc\Scramble\Scramble;

Route::get('/', function () {
    return view("welcome");
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

// API Docs routes
Scramble::registerUiRoute('docs/public',    api: 'public');
Scramble::registerJsonSpecificationRoute('docs/public.json', api: 'public');

Scramble::registerUiRoute('docs/admin',     api: 'admin');
Scramble::registerJsonSpecificationRoute('docs/admin.json', api: 'admin');

Scramble::registerUiRoute('docs/village',   api: 'village');
Scramble::registerJsonSpecificationRoute('docs/village.json', api: 'village');

Scramble::registerUiRoute('docs/provider',  api: 'provider');
Scramble::registerJsonSpecificationRoute('docs/provider.json', api: 'provider');

Scramble::registerUiRoute('docs/maintenance_provider', api: 'maintenance_provider');
Scramble::registerJsonSpecificationRoute('docs/maintenance_provider.json', api: 'maintenance_provider');

Scramble::registerUiRoute('docs/user',      api: 'user');
Scramble::registerJsonSpecificationRoute('docs/user.json', api: 'user');

Scramble::registerUiRoute('docs/security',  api: 'security');
Scramble::registerJsonSpecificationRoute('docs/security.json', api: 'security');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
