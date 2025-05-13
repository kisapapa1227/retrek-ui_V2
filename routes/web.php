<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RetrekController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	return view('entry');
});

Route::get('/home', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth'])->group(function () {
#    Route::get('/user', [RetrekController::class, 'user'])->name('user');
    Route::get('/user', [RetrekController::class, 'myLogout'])->name('myLogout');
    Route::get('/tmp', [RetrekController::class, 'tmp'])->name('tmp');
    Route::post('/exepy', [RetrekController::class, 'exepy'])->name('exepy');
    
    Route::post('/add', [RetrekController::class, 'add'])->name('add');
    Route::post('/remove', [RetrekController::class, 'remove'])->name('remove');
    Route::post('/favorite', [RetrekController::class, 'favorite'])->name('favorite');
    Route::post('/syncPdf', [RetrekController::class, 'syncPdf'])->name('syncPdf');
    Route::post('/askProc', [RetrekController::class, 'askProc'])->name('askProc');
    Route::post('/db', [RetrekController::class, 'db'])->name('db');
    Route::post('/addDb', [RetrekController::class, 'addDb'])->name('addDb');
    Route::post('/dropDb', [RetrekController::class, 'dropDb'])->name('dropDb');
    Route::post('/dbAction', [RetrekController::class, 'dbAction'])->name('dbAction');
    Route::post('/multiProc', [RetrekController::class, 'multiProc'])->name('multiProc');
    Route::get('/kRet', [RetrekController::class, 'kRet'])->name('kRet');
    Route::get('/dummyEntry', [RetrekController::class, 'dummyEntry'])->name('dummyEntry');
    Route::get('/myLogout', [RetrekController::class, 'myLogout'])->name('myLogout');
    Route::get('/singleSearch', [RetrekController::class, 'singleSearch'])->name('singleSearch');
    Route::get('/multiSearch', [RetrekController::class, 'multiSearch'])->name('multiSearch');
    Route::get('/dbManage', [RetrekController::class, 'dbManage'])->name('dbManage');
});
