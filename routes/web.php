<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditorController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\NodeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PublishedPageController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/workspaces/{workspace}/websites', [WebsiteController::class, 'store'])->name('websites.store');
    Route::delete('/websites/{website}', [WebsiteController::class, 'destroy'])->name('websites.destroy');
    Route::post('/websites/{website}/pages', [PageController::class, 'store'])->name('pages.store');
    Route::patch('/websites/{website}/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::post('/websites/{website}/pages/{page}/duplicate', [PageController::class, 'duplicate'])->name('pages.duplicate');
    Route::delete('/websites/{website}/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    Route::get('/websites/{website}/pages/{page}/edit', EditorController::class)->name('editor');
    Route::post('/websites/{website}/pages/{page}/nodes', [NodeController::class, 'store'])->name('nodes.store');
    Route::patch('/websites/{website}/pages/{page}/nodes/{node}', [NodeController::class, 'update'])->name('nodes.update');
    Route::post('/websites/{website}/pages/{page}/nodes/{node}/move', [NodeController::class, 'move'])->name('nodes.move');
    Route::post('/websites/{website}/pages/{page}/nodes/{node}/duplicate', [NodeController::class, 'duplicate'])->name('nodes.duplicate');
    Route::post('/websites/{website}/pages/{page}/checkpoint', [EditorController::class, 'checkpoint'])->name('pages.checkpoint');
    Route::post('/websites/{website}/pages/{page}/templates/apply', [TemplateController::class, 'apply'])->name('templates.apply');
    Route::post('/websites/{website}/pages/{page}/nodes/{node}/save-template', [TemplateController::class, 'saveSection'])->name('templates.save-section');
    Route::delete('/websites/{website}/pages/{page}/nodes/{node}', [NodeController::class, 'destroy'])->name('nodes.destroy');
    Route::post('/websites/{website}/publish', [WebsiteController::class, 'publish'])->name('websites.publish');
    Route::get('/websites/{website}/content', ContentController::class)->name('content');
    Route::post('/workspaces/{workspace}/assets', [AssetController::class, 'store'])->name('assets.store');
    Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
    Route::post('/websites/{website}/collections', [CmsController::class, 'storeCollection'])->name('collections.store');
    Route::post('/websites/{website}/collections/{collection}/fields', [CmsController::class, 'storeField'])->name('fields.store');
    Route::post('/websites/{website}/collections/{collection}/items', [CmsController::class, 'storeItem'])->name('items.store');
    Route::post('/websites/{website}/collections/{collection}/items/{item}/publish', [CmsController::class, 'publishItem'])->name('items.publish');
});

Route::get('/sites/{website}/{page?}', PublishedPageController::class)
    ->where(['website' => '[a-z0-9-]+', 'page' => '[a-z0-9-]+'])
    ->name('published.page');
Route::post('/sites/{website}/forms/{form}', [FormSubmissionController::class, 'store'])->middleware('throttle:10,1')->name('published.forms.submit');
