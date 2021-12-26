<?php

use App\Features\UserImpersonation;
use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Stancl\Tenancy\Middleware\ScopeSessions;
use Stancl\Tenancy\Middleware\CheckTenantForMaintenanceMode;

Admin::routes();

/**
 * 管理员可以通过此路由进入租户后台.
 */
Route::middleware([
    'web',
    CheckTenantForMaintenanceMode::class,
    ScopeSessions::class,
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])
    ->prefix(config('admin.route.prefix'))
    ->namespace(config('admin.route.namespace'))
    ->group(function (Router $router) {
        $router->get('/god/{token}', function ($token) {
            return UserImpersonation::makeResponse($token);
        });

        $router->get('/dashboard', 'HomeController@index');
        $router->resource('/categories', 'CategoryController');
        $router->resource('/topics', 'TopicController');
        $router->resource('/replies', 'ReplyController');
        $router->resource('/links', 'LinkController');

    });

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    // 仪表盘
    
});
