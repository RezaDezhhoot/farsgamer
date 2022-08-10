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
Route::get('/welcome', function (){
    return view('welcome');
});
Route::middleware(['auth','role:admin'])->get('/',\App\Http\Livewire\Admin\Dashboards\Dashboard::class)->name('home');
// admin
Route::middleware(['auth','role:admin'])->namespace('App\Http\Livewire\Admin')->prefix('/admin')->group(function ()
{
    // public
    Route::get('/dashboard', Dashboards\Dashboard::class)->name('admin.dashboard');
    Route::get('/profile', Profile\IndexProfile::class)->name('admin.profile');
    Route::get('/my-chats', MyChats\IndexMyChats::class)->name('admin.myChats');
    Route::get('/orders', Orders\IndexOrder::class)->name('admin.order');
    Route::get('/orders/{action}/{id?}', Orders\StoreOrder::class)->name('admin.store.order');
    Route::get('/order-transactions', OrderTransactions\IndexOrderTransaction::class)->name('admin.transaction');
    Route::get('/order-transactions/{action}/{id?}', OrderTransactions\StoreOrderTransaction::class)->name('admin.store.transaction');
    Route::get('/chats', Chats\IndexChat::class)->name('admin.chat');
    Route::get('/tickets', Tickets\IndexTicket::class)->name('admin.ticket');
    Route::get('/tickets/{action}/{id?}', Tickets\StoreTicket::class)->name('admin.store.ticket');
    Route::get('/comments', Comments\IndexComment::class)->name('admin.comment');
    Route::get('/comments/{action}/{id?}', Comments\StoreComment::class)->name('admin.store.comment');
    Route::get('/notifications', Notifications\IndexNotification::class)->name('admin.notification');
    Route::get('/notifications/{action}/{id?}', Notifications\StoreNotification::class)->name('admin.store.notification');
    Route::get('/offends', Offends\IndexOffend::class)->name('admin.offend');
    // content
    Route::get('/sends', Sends\IndexSend::class)->name('admin.send');
    Route::get('/sends/{action}/{id?}', Sends\StoreSend::class)->name('admin.store.send');
    Route::get('/platforms', Platforms\IndexPlatform::class)->name('admin.platform');
    Route::get('/platforms/{action}/{id?}', Platforms\StorePlatform::class)->name('admin.store.platform');
    Route::get('/categories', Categories\IndexCategory::class)->name('admin.category');
    Route::get('/categories/{action}/{id?}', Categories\StoreCategory::class)->name('admin.store.category');
    Route::get('/articleCategories', ArticlesCategories\IndexArticleCategory::class)->name('admin.articleCategory');
    Route::get('/articleCategories/{action}/{id?}', ArticlesCategories\StoreArticleCategory::class)->name('admin.store.articleCategory');
    Route::get('/articles', Articles\IndexArticle::class)->name('admin.article');
    Route::get('/articles/{action}/{id?}', Articles\StoreArticle::class)->name('admin.store.article');
    // financial
    Route::get('/cards', Cards\IndexCard::class)->name('admin.card');
    Route::get('/cards/{action}/{id?}', Cards\StoreCard::class)->name('admin.store.card');
    Route::get('/requests', Financial\Requests\IndexRequest::class)->name('admin.request');
    Route::get('/requests/{action}/{id?}', Financial\Requests\StoreRequest::class)->name('admin.store.request');
    Route::get('/payments', Financial\Payments\IndexPayment::class)->name('admin.payment');
    Route::get('/payments/{action}/{id?}', Financial\Payments\StorePayment::class)->name('admin.store.payment');
    // technical
    Route::get('/securities', Securities\IndexSecurity::class)->name('admin.security');
    Route::get('/tasks', Tasks\IndexTask::class)->name('admin.task');
    Route::get('/tasks/{action}/{id?}', Tasks\StoreTask::class)->name('admin.store.task');
    Route::get('/users', Users\IndexUser::class)->name('admin.user');
    Route::get('/users/{action}/{id?}', Users\StoreUser::class)->name('admin.store.user');
    Route::get('/roles', Roles\IndexRole::class)->name('admin.role');
    Route::get('/reports', Reports\Logs\IndexLog::class)->name('admin.report');
    Route::get('/roles/{action}/{id?}', Roles\StoreRole::class)->name('admin.store.role');
    Route::get('/settings/base', Settings\BaseSetting::class)->name('admin.setting.base');
    Route::get('/settings/home', Settings\HomeSetting::class)->name('admin.setting.home');
    Route::get('/settings/about-us', Settings\AboutUsSetting::class)->name('admin.setting.aboutUs');
    Route::get('/settings/contact-us', Settings\ContactUsSetting::class)->name('admin.setting.contactUs');
    Route::get('/settings/law', Settings\LawSetting::class)->name('admin.setting.law');
    Route::get('/settings/law/{action}/{id?}', Settings\CreateLaw::class)->name('admin.setting.law.create');
    Route::get('/settings/chatLaw', Settings\ChatLawSetting::class)->name('admin.setting.chatLaw');
    Route::get('/settings/chatLaw/{action}/{id?}', Settings\CreateChatLaw::class)->name('admin.setting.chatLaw.create');
    Route::get('/settings/fag', Settings\QuestionSetting::class)->name('admin.setting.fag');
    Route::get('/settings/fag/{action}/{id?}', Settings\CreateFag::class)->name('admin.setting.fag.create');
});
// file manager
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['role:admin']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});
// logout
Route::get('/logout', function (){
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('auth');
})->name('logout');
// web auth
Route::middleware('guest')->group(function () {
    Route::get('/auth', \App\Http\Livewire\Site\Auth\Auth::class)->name('auth');
});
// checkout
Route::get('/verify/{gateway}',\App\Http\Livewire\Cart\CallBack::class);
