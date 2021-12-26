<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\CheckTenantForMaintenanceMode;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    CheckTenantForMaintenanceMode::class,
    PreventAccessFromCentralDomains::class,
    ScopeSessions::class,
])->group(function () {
    Route::get('/a', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });
    //Route::get('/', 'PagesController@root')->name('root');
    Route::get('/', 'TopicsController@index')->name('root');
    //Auth::routes();
    // 用户身份验证相关的路由
	Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
	Route::post('login', 'Auth\LoginController@login');
	Route::post('logout', 'Auth\LoginController@logout')->name('logout');

	// 用户注册相关路由
	Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
	Route::post('register', 'Auth\RegisterController@register');

	// 密码重置相关路由
	Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
	Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
	Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
	Route::post('password/reset', 'Auth\ResetPasswordController@reset')->name('password.update');

	// Email 认证相关路由
	Route::get('email/verify', 'Auth\VerificationController@show')->name('verification.notice');
	Route::get('email/verify/{id}/{hash}', 'Auth\VerificationController@verify')->name('verification.verify');
	Route::post('email/resend', 'Auth\VerificationController@resend')->name('verification.resend');
	Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

	Route::resource('users', 'UsersController', ['only' => ['show', 'update', 'edit']]);

	Route::resource('topics', 'TopicsController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);
	Route::get('topics/{topic}/{slug?}', 'TopicsController@show')->name('topics.show');

	Route::resource('categories', 'CategoriesController', ['only' => ['show']]);
	Route::post('upload_image', 'TopicsController@uploadImage')->name('topics.upload_image');

	Route::resource('replies', 'RepliesController', ['only' => ['store', 'destroy']]);
	Route::resource('notifications', 'NotificationsController', ['only' => ['index']]);



});







//api部分：
Route::middleware([
    'api',
    InitializeTenancyByDomain::class,
    CheckTenantForMaintenanceMode::class,
    PreventAccessFromCentralDomains::class,
    //ScopeSessions::class,
])->group(function () {
	Route::prefix('api/v1')->namespace('Api')->name('api.v1.')->group(function() {

	    Route::middleware('throttle:' . config('api.rate_limits.sign'))
            ->group(function () {
            	// 图片验证码
                Route::post('captchas', 'CaptchasController@store')
                    ->name('captchas.store');
                // 短信验证码
                Route::post('verificationCodes', 'VerificationCodesController@store')
                    ->name('verificationCodes.store');
               
                    // 用户注册
                Route::post('users', 'UsersController@store')
                    ->name('users.store');
                // 某个用户的详情
                Route::get('users/{user}', 'UsersController@show')
                    ->name('users.show');
                // 话题列表，详情
	            Route::resource('topics', 'TopicsController')->only([
	                    'index', 'show'
	            ]);

                // 登录后可以访问的接口
                Route::middleware('auth:api')->group(function() {
                    // 当前登录用户信息
                    Route::get('user', 'UsersController@me')
                        ->name('user.show');

                    // 上传图片
                    Route::post('images', 'ImagesController@store')
                        ->name('images.store');
                    // 编辑登录用户信息
                    Route::patch('user', 'UsersController@update')
                        ->name('user.update');

                    // 分类列表
                	Route::get('categories', 'CategoriesController@index')
                    	->name('categories.index');
                    
	                // 发布话题
                    Route::resource('topics', 'TopicsController')->only([
                        'store', 'update', 'destroy'
                    ]);
                });

                // 第三方登录
                Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
                    ->where('social_type', 'wechat')
                    ->name('socials.authorizations.store');
                // 登录
                Route::post('authorizations', 'AuthorizationsController@store')
                    ->name('authorizations.store');
                // 刷新token
                Route::put('authorizations/current', 'AuthorizationsController@update')
                    ->name('authorizations.update');
                // 删除token
                Route::delete('authorizations/current', 'AuthorizationsController@destroy')
                    ->name('authorizations.destroy');
        });

        Route::middleware('throttle:' . config('api.rate_limits.access'))
            ->group(function () {

                

        });

	});
	 
});