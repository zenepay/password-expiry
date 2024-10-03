<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Filament\Facades\Filament;

Route::get('password-expired', function () {
    if (class_exists(\App\Http\Controllers\Auth\PasswordResetLinkController::class)) {
        return App\Http\Controllers\Auth\PasswordResetLinkController::expired();
    } elseif (class_exists(Filament::class)) {
        foreach (Filament::getPanels() as $panel) {
            if ($panel->hasPasswordReset()) {
                return redirect()->route('filament.admin.auth.password-reset.request');
            }
        }
    }
})->name('password.renew');
