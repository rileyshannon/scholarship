<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::index')->name('index');
Route::livewire('/faq', 'pages::faq')->name('faq');

Route::livewire('/application', 'pages::application.create')->name('application.create');
Route::livewire('/application/success', 'pages::application.success')->name('application.success');

Route::middleware(['auth', 'verified', 'is_not_admin'])->name('grader.')->group(function () {
    Route::livewire('/dashboard', 'pages::grader.dashboard')->name('dashboard');
    Route::livewire('/grade/{application}', 'pages::grader.grade.show')->name('grade.show');
});

Route::middleware(['auth', 'verified', 'is_admin'])->name('admin.')->prefix('admin')->group(function () {
    Route::livewire('/dashboard', 'pages::admin.dashboard')->name('dashboard');
    Route::livewire('/applications', 'pages::admin.applications.index')->name('applications.index');
    Route::livewire('/applications/{application}', 'pages::admin.applications.show')->name('applications.show');
    Route::livewire('/users', 'pages::admin.users.index')->name('users.index');
});

require __DIR__.'/settings.php';
