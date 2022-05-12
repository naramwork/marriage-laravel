<?php

use App\Http\Livewire\AdminsControl;
use App\Http\Livewire\CustomersControl;

use App\Http\Livewire\MarriageRequestPage;
use App\Http\Livewire\MessagePage;
use App\Http\Livewire\NotificationPage;
use App\Http\Livewire\UserMessagePage;
use App\Http\Livewire\UserProfilePage;
use App\Http\Livewire\Visitor;
use App\Http\Middleware\CheckStatus;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect(route('dashboard'));
});
Route::get('/privacy', function () {
    return view('privacy');
});


Route::get('/visitor', Visitor::class)->name('visitor');

// Route::get('/generate', function () {
//     $targetFolder = '/home/naramapps/public_html/laravel/storage/app/public';
//     $linkFolder = '/home/naramapps/public_html/storage';
//     symlink($targetFolder, $linkFolder);
//     echo 'Symlink completed';
// })->name('generate');

Route::group(['middleware' => ['auth:sanctum', 'verified', CheckStatus::class]], function () {

    Route::get('/dashboard', function () {
       return view('dashboard');
    })->name('dashboard');


    Route::get('/notification', NotificationPage::class)->name('notification');

    
    Route::group(['middleware' => ['can:control']], function () {
        Route::get('/admin/admins-control', AdminsControl::class)->name('admins-control');
        Route::get('/admin/customers-control', CustomersControl::class)->name('customers-control');
        Route::get('/admin/user-message', UserMessagePage::class)->name('user-message');
    });


    Route::group(['middleware' => ['can:observe']], function () {
        Route::get('/user/{id}', UserProfilePage::class)->name('user-profile-page');
        Route::get('/messages', MessagePage::class)->name('messages');
        Route::get('/marriage_request', MarriageRequestPage::class)->name('marriage-request');
    });
});
