<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RustProxyController;
use App\Http\Controllers\RustProxySettingController;
use App\Http\Controllers\UpstreamController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::get('/settings', [SettingsController::class, 'edit'])->name('settings.edit');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');

Route::get('/proxy-settings', [RustProxySettingController::class, 'index'])->name('rustproxysettings');
Route::put('/proxy-settings', [RustProxySettingController::class, 'update'])->name('rustproxysettings.update');

Route::resource('proxy', RustProxyController::class, ['parameters' => ['proxy' => 'rustProxy']]);

Route::get('/upstream/{upstream}/edit', [UpstreamController::class, 'edit'])->name('upstream.edit');
Route::put('/upstream/{upstream}', [UpstreamController::class, 'update'])->name('upstream.update');
Route::delete('/upstreams/{upstream}', [UpstreamController::class, 'destroy'])->name('upstream.destroy');

Route::get('/rustproxy/{rustProxy}/upstreams', [UpstreamController::class, 'index'])->name('upstream.list');
Route::post('/rustproxy/{rustProxy}/upstreams', [UpstreamController::class, 'store'])->name('upstream.store');
