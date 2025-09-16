<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\GeozoneController;
use App\Http\Controllers\AttendanceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });


Route::get('/', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');

// Rute untuk memproses data login (form submission)
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Rute yang hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard.dashboard-admin.dashboard');
    })->name('dashboard');

    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::resource('users', UserController::class);
    Route::get('/recognition', function () {
        return view('pages.recognition.recognition');
    });
    
    // routes/web.php 
    // Route::get('/server-time', function () {
    //     return response()->json([
    //         'time' => \Carbon\Carbon::now('Asia/Jakarta')->format('H:i:s'),
    //         'date' => \Carbon\Carbon::now('Asia/Jakarta')->toDateString(),
    //     ]);
    // });

    // Route::get('/roles', [RoleController::class]);
    // Route::get('/roles/get-data', [RoleController::class, 'getData'])->name('roles.getData');

    // Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    //  Route::resource('roles', RoleController::class);
    // Route::get('/roles', [RoleController::class, 'index']);


    Route::get('/me', [UserController::class, 'profile']);
});


Route::middleware(['auth'])->group(function () {
    // Roles dengan permission check
     Route::resource('roles', RoleController::class)->middleware([
        'permission:read roles'
    ]);
});




// Rute untuk logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

