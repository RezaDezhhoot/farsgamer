<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Permission;
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

Route::get('/',\App\Http\Livewire\Site\Home\IndexHome::class)->name('home');
Route::get('/saved',\App\Http\Livewire\Site\Dashboard\Others\IndexSaved::class)->name('save');
Route::get('/orders/{userID}/{id}/{slug}',\App\Http\Livewire\Site\Orders\SingleOrder::class)->name('order');
Route::get('/users/{user}',\App\Http\Livewire\Site\Users\SingleUser::class)->name('user');

Route::middleware(['auth','role:admin','schedule'])->prefix('/admin')->group(function ()
{
    // public
    Route::get('/dashboard', App\Http\Livewire\Admin\Dashboards\Dashboard::class)->name('admin.dashboard');
    Route::get('/profile', App\Http\Livewire\Admin\Profile\IndexProfile::class)->name('admin.profile');
    Route::get('/my-chats', App\Http\Livewire\Admin\MyChats\IndexMyChats::class)->name('admin.myChats');
    Route::get('/orders', App\Http\Livewire\Admin\Orders\IndexOrder::class)->name('admin.order');
    Route::get('/orders/{action}/{id?}', App\Http\Livewire\Admin\Orders\StoreOrder::class)->name('admin.store.order');
    Route::get('/order-transactions', App\Http\Livewire\Admin\OrderTransactions\IndexOrderTransaction::class)->name('admin.transaction');
    Route::get('/order-transactions/{action}/{id?}', App\Http\Livewire\Admin\OrderTransactions\StoreOrderTransaction::class)->name('admin.store.transaction');
    Route::get('/chats', App\Http\Livewire\Admin\Chats\IndexChat::class)->name('admin.chat');
    Route::get('/tickets', App\Http\Livewire\Admin\Tickets\IndexTicket::class)->name('admin.ticket');
    Route::get('/tickets/{action}/{id?}', App\Http\Livewire\Admin\Tickets\StoreTicket::class)->name('admin.store.ticket');
    Route::get('/comments', App\Http\Livewire\Admin\Comments\IndexComment::class)->name('admin.comment');
    Route::get('/comments/{action}/{id?}', App\Http\Livewire\Admin\Comments\StoreComment::class)->name('admin.store.comment');
    Route::get('/notifications', App\Http\Livewire\Admin\Notifications\IndexNotification::class)->name('admin.notification');
    Route::get('/notifications/{action}/{id?}', App\Http\Livewire\Admin\Notifications\StoreNotification::class)->name('admin.store.notification');
    // content
    Route::get('/sends', App\Http\Livewire\Admin\Sends\IndexSend::class)->name('admin.send');
    Route::get('/sends/{action}/{id?}', App\Http\Livewire\Admin\Sends\StoreSend::class)->name('admin.store.send');
    Route::get('/platforms', App\Http\Livewire\Admin\Platforms\IndexPlatform::class)->name('admin.platform');
    Route::get('/platforms/{action}/{id?}', App\Http\Livewire\Admin\Platforms\StorePlatform::class)->name('admin.store.platform');
    Route::get('/categories', App\Http\Livewire\Admin\Categories\IndexCategory::class)->name('admin.category');
    Route::get('/categories/{action}/{id?}', App\Http\Livewire\Admin\Categories\StoreCategory::class)->name('admin.store.category');
    Route::get('/addresses', App\Http\Livewire\Admin\Addresses\IndexAddress::class)->name('admin.address');
    Route::get('/addresses/{action}/{id?}', App\Http\Livewire\Admin\Addresses\StoreAddress::class)->name('admin.store.address');
    Route::get('/articleCategories', App\Http\Livewire\Admin\ArticlesCategories\IndexArticleCategory::class)->name('admin.articleCategory');
    Route::get('/articleCategories/{action}/{id?}', App\Http\Livewire\Admin\ArticlesCategories\StoreArticleCategory::class)->name('admin.store.articleCategory');
    Route::get('/articles', App\Http\Livewire\Admin\Articles\IndexArticle::class)->name('admin.article');
    Route::get('/articles/{action}/{id?}', App\Http\Livewire\Admin\Articles\StoreArticle::class)->name('admin.store.article');
    // financial
    Route::get('/cards', App\Http\Livewire\Admin\Cards\IndexCard::class)->name('admin.card');
    Route::get('/cards/{action}/{id?}', App\Http\Livewire\Admin\Cards\StoreCard::class)->name('admin.store.card');
    Route::get('/requests', App\Http\Livewire\Admin\Financial\Requests\IndexRequest::class)->name('admin.request');
    Route::get('/requests/{action}/{id?}', App\Http\Livewire\Admin\Financial\Requests\StoreRequest::class)->name('admin.store.request');
    Route::get('/payments', App\Http\Livewire\Admin\Financial\Payments\IndexPayment::class)->name('admin.payment');
    Route::get('/payments/{action}/{id?}', App\Http\Livewire\Admin\Financial\Payments\StorePayment::class)->name('admin.store.payment');
    // technical
    Route::get('/securities', App\Http\Livewire\Admin\Securities\IndexSecurity::class)->name('admin.security');
    Route::get('/tasks', App\Http\Livewire\Admin\Tasks\IndexTask::class)->name('admin.task');
    Route::get('/tasks/{action}/{id?}', App\Http\Livewire\Admin\Tasks\StoreTask::class)->name('admin.store.task');
    Route::get('/users', App\Http\Livewire\Admin\Users\IndexUser::class)->name('admin.user');
    Route::get('/users/{action}/{id?}', App\Http\Livewire\Admin\Users\StoreUser::class)->name('admin.store.user');
    Route::get('/roles', App\Http\Livewire\Admin\Roles\IndexRole::class)->name('admin.role');
    Route::get('/roles/{action}/{id?}', App\Http\Livewire\Admin\Roles\StoreRole::class)->name('admin.store.role');
    Route::get('/settings/base', App\Http\Livewire\Admin\Settings\BaseSetting::class)->name('admin.setting.base');
    Route::get('/settings/home', App\Http\Livewire\Admin\Settings\HomeSetting::class)->name('admin.setting.home');
    Route::get('/settings/about-us', App\Http\Livewire\Admin\Settings\AboutUsSetting::class)->name('admin.setting.aboutUs');
    Route::get('/settings/contact-us', App\Http\Livewire\Admin\Settings\ContactUsSetting::class)->name('admin.setting.contactUs');
    Route::get('/settings/law', App\Http\Livewire\Admin\Settings\LawSetting::class)->name('admin.setting.law');
    Route::get('/settings/law/{action}/{id?}', App\Http\Livewire\Admin\Settings\CreateLaw::class)->name('admin.setting.law.create');
    Route::get('/settings/chatLaw', App\Http\Livewire\Admin\Settings\ChatLawSetting::class)->name('admin.setting.chatLaw');
    Route::get('/settings/chatLaw/{action}/{id?}', App\Http\Livewire\Admin\Settings\CreateChatLaw::class)->name('admin.setting.chatLaw.create');
    Route::get('/settings/fag', App\Http\Livewire\Admin\Settings\QuestionSetting::class)->name('admin.setting.fag');
    Route::get('/settings/fag/{action}/{id?}', App\Http\Livewire\Admin\Settings\CreateFag::class)->name('admin.setting.fag.create');
});

// user
Route::middleware(['auth'])->prefix('/user')->group(function (){
    Route::get('/dashboard', App\Http\Livewire\Site\Dashboard\Dashboards\IndexDashboard::class)->name('user.dashboard')->middleware('userAuth');
    Route::get('/profile', App\Http\Livewire\Site\Dashboard\Profile\IndexProfile::class)->name('user.profile')->middleware('userAuth');
    Route::get('/auth', App\Http\Livewire\Site\Dashboard\Profile\AuthComponent::class)->name('user.auth');
    Route::get('/chats', App\Http\Livewire\Site\Dashboard\Chats\IndexChat::class)->name('user.chat')->middleware('userAuth');
    Route::get('/orders', App\Http\Livewire\Site\Dashboard\Orders\IndexOrder::class)->name('user.order')->middleware('userAuth');
    Route::get('/orders/{action}/{id?}', App\Http\Livewire\Site\Dashboard\Orders\StoreOrder::class)->name('user.store.order')->middleware('userAuth');
    Route::get('/requests', App\Http\Livewire\Site\Dashboard\Accountings\IndexAccounting::class)->name('user.accounting')->middleware('userAuth');
    Route::get('/requests/{action}/{id?}', App\Http\Livewire\Site\Dashboard\Accountings\StoreAccounting::class)->name('user.store.accounting')->middleware('userAuth');
    Route::get('/order-transactions', App\Http\Livewire\Site\Dashboard\Transactions\IndexTransaction::class)->name('user.transaction')->middleware('userAuth');
    Route::get('/order-transactions/{action}/{id?}', App\Http\Livewire\Site\Dashboard\Transactions\StoreTransaction::class)->name('user.store.transaction')->middleware('userAuth');
    Route::get('/call-back',\App\Http\Livewire\Cart\CallBack::class)->name('callBack');
});
// auth
Route::middleware('guest')->group(function () {
    Route::get('/auth', \App\Http\Livewire\Site\Auth\Auth::class)->name('auth');
});
Route::get('/logout', function (){
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('auth');
})->name('logout');

// file manager
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
