<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/test', function(){
    $cookie = Cookie::get('locale');
    exit($cookie);
});

# Index route
Route::get('/', function () {
    $locale = 'es';

    App::setLocale($locale);

    if(Auth::user() != null)
        return redirect('/home');

    $response = new Response(view('emediamarket'));
    $response->withCookie(cookie('locale', $locale, 45000));
    return $response;
});

#Español
Route::get('/es', function () {
    $locale = 'es';

    App::setLocale($locale);

    if(Auth::user() != null)
        return redirect('/home');

    $response = new Response(view('emediamarket'));
    $response->withCookie(cookie('locale', $locale, 45000));
    return $response;
});

#Ingles
Route::get('/en', function () {
    $locale = 'en';

    App::setLocale($locale);

    if(Auth::user() != null)
        return redirect('/home');

    $response = new Response(view('emediamarket'));
    $response->withCookie(cookie('locale', $locale, 45000));
    return $response;
});

Route::get('/faq', function(){
    if(Auth::user() != null)
        return view('insite-faq');

    $locale = Cookie::get('locale');
    App::setLocale($locale);
    return view('faq');
});


# Logout route
Route::get('/logout', function(){
   Auth::logout();
   return view('emediamarket');
});

Route::get('lang/{lang}', ['as'=>'lang.switch', 'uses'=>'LanguageController@switchLang']);

# Auth routes for all users
Auth::routes();
Route::get('password/reset/{token}', ['as' => 'password.reset', 'uses' => 'Auth\ResetPasswordController@showResetForm']);
Route::post('password/reset','Auth\ResetPasswordController@reset');

# Token Verification route
Route::get('/register/verify/{token}','Auth\RegisterController@verify');

# Auth enabled routes
Route::group(['middleware' => ['auth']], function(){

    # Home
    Route::get('/home', ['as' => 'home', 'uses' => 'HomeController@index']);

    # Users routes
    Route::group(['prefix' => 'users'], function (){
        Route::get('', ['as' => 'users.index', 'uses' => 'UserController@index']);
        Route::get('{id}', ['as' => 'users.show', 'uses' => 'UserController@show']);
        Route::get('{id}/edit', ['as' => 'users.edit', 'uses' => 'UserController@edit']);
        Route::post('{id}', ['as' => 'users.update', 'uses' => 'UserController@update']);
        Route::get('{id}/password', ['as' => 'users.password', 'uses' => 'UserController@showPasswordForm']);
        Route::post('{id}/change-password', ['as' => 'password.change', 'uses' => 'UserController@changePassword']);
    });

    # Messaging routes
    Route::group(['prefix' => 'messages'], function () {
        Route::get('/', ['as' => 'messages', 'uses' => 'MessagesController@index']);
        Route::get('{id}', ['as' => 'messages.show', 'uses' => 'MessagesController@show']);
        Route::put('{id}', ['as' => 'messages.update', 'uses' => 'MessagesController@update']);
    });

    # Addspaces access for everyone
    Route::resource('addspaces', 'AddspaceController', ['only' => ['index', 'show']]);

    Route::get('wallet', ['as' => 'wallet', 'uses' => 'WalletController@index']);

    # Manager specific routes
    Route::group(['middleware' => ['manager']], function(){

        # Addspaces routes
        Route::resource('addspaces', 'AddspaceController', ['except' => ['index', 'create' ,'show', 'delete']]);
        Route::get('addspaces/{id}/pause', ['as' => 'addspaces.pause', 'uses' => 'AddspaceController@pause']);
        Route::get('addspaces/{id}/close', ['as' => 'addspaces.close', 'uses' => 'AddspaceController@close']);
        Route::get('addspaces/{id}/activate', ['as' => 'addspaces.activate', 'uses' => 'AddspaceController@activate']);

        Route::post('addspace/{id}/reject', ['as' => 'addspaces.reject', 'uses' => 'WalletController@rejectPayment']);
        Route::post('addspace/{id}/accept', ['as' => 'addspaces.accept', 'uses' => 'WalletController@acceptPayment']);

        # User management routes
        Route::group(['prefix' => 'users'], function () {
            Route::get('create',['as' => 'users.create', 'uses' => 'UserController@create']);
            Route::post('/',['as' => 'users.store', 'uses' => 'UserController@store']);
            Route::get('{id}/activate',['as' => 'users.activate', 'uses' => 'UserController@activate']);
            Route::get('{id}/deactivate',['as' => 'users.deactivate', 'uses' => 'UserController@deactivate']);
            Route::post('{id}/password',['as' => 'users.sendpassword', 'uses' => 'UserController@sendPassword']);
        });


        Route::get('transactions', ['as' => 'transactions', 'uses' => 'WalletController@transactions']);
        Route::get('revenues', ['as' => 'revenues', 'uses' => 'WalletController@revenues']);

        Route::get('profits', ['as' => 'profits.index', 'uses' => 'ProfitController@index']);
        Route::post('profits/change/{id}', ['as' => 'profits.change', 'uses' => 'ProfitController@change']);
        Route::post('profits/default/{id}', ['as' => 'profits.default', 'uses' => 'ProfitController@applyDefault']);
        Route::post('profits', ['as' => 'profits.store', 'uses' => 'ProfitController@store']);
        Route::get('profits/edit', ['as' => 'profits.edit', 'uses' => 'ProfitController@edit']);
        Route::post('profits/edit', ['as' => 'profits.update', 'uses' => 'ProfitController@update']);

        Route::get('packages', ['as' => 'packages', 'uses' => 'WalletController@packages']);
        Route::get('packages/create', ['as' => 'package.create', 'uses' => 'CreditPackageController@create']);
        Route::post('packages', ['as' => 'package.store', 'uses' => 'CreditPackageController@store']);
        Route::get('packages/{id}/edit', ['as' => 'package.edit', 'uses' => 'CreditPackageController@edit']);
        Route::post('packages/{id}', ['as' => 'package.update', 'uses' => 'CreditPackageController@update']);
        Route::get('packages/{id}/activate', ['as' => 'package.activate', 'uses' => 'CreditPackageController@activate']);
        Route::get('packages/{id}/deactivate', ['as' => 'package.deactivate', 'uses' => 'CreditPackageController@deactivate']);

        # Withdrawal flow routes
        Route::get('withdrawal', ['as' => 'withdrawal.index','uses' => 'WalletController@withdrawals',]);
        Route::get('withdrawal/{transaction_id}/authorize', ['as' => 'withdrawal.show','uses' => 'WalletController@showWithdrawal',]);
        Route::post('withdrawal/{transaction_id}', ['as' => 'withdrawal.authorize','uses' => 'WalletController@authorizeWithdrawal',]);
    });

    # Editor specific routes
    Route::group(['middleware' => ['editor']], function(){

        # Addspaces routes
        Route::resource('addspaces', 'AddspaceController', ['except' => ['index', 'show']]);
        Route::post('addspaces/store', ['as' => 'addspaces.store', 'uses' => 'AddspaceController@store']);

        Route::get('addspaces/{id}/pause', ['as' => 'addspaces.pause', 'uses' => 'AddspaceController@pause']);
        Route::get('addspaces/{id}/close', ['as' => 'addspaces.close', 'uses' => 'AddspaceController@close']);
        Route::get('addspaces/{id}/activate', ['as' => 'addspaces.activate', 'uses' => 'AddspaceController@activate']);

        Route::post('addspaces', ['as' => 'addspaces.indexFilter', 'uses' => 'AddspaceController@indexFilter']);

        Route::get('sales', ['as' => 'sales', 'uses' => 'WalletController@sales']);

        Route::get('packages', ['as' => 'packages', 'uses' => 'WalletController@packages']);

        # Withdrawal request routes
        Route::post('request-withdraw', ['as' => 'withdraw','uses' => 'WalletController@withdraw']);
    });

    # Advertiser specific routes
    Route::group(['middleware' => ['advertiser']], function(){

        Route::get('/addspaces/search', ['as' => 'addspaces.search', 'uses' => 'AddspaceController@search']);
        Route::post('/addspaces/search', ['as' => 'addspaces.filter', 'uses' => 'AddspaceController@filter']);

        Route::get('packages', ['as' => 'packages', 'uses' => 'WalletController@packages']);
        Route::get('payments', ['as' => 'payments', 'uses' => 'WalletController@payments']);

        # Payment routes
        Route::post('addspace/{id}/charge', ['as' => 'addspaces.charge', 'uses' => 'WalletController@charge']);
        Route::post('addspace/{id}/user-reject', ['as' => 'addspaces.user_reject', 'uses' => 'WalletController@rejectPaymentByUser']);
        Route::post('addspace/{id}/accept', ['as' => 'addspaces.accept', 'uses' => 'WalletController@acceptPayment']);

        # Deposit request routes
        Route::get('deposit', ['as' => 'deposit', 'uses' => 'WalletController@showDepositForm']);
        Route::post('deposit', ['as' => 'deposit.prepare', 'uses' => 'WalletController@prepareDeposit']);

        # Deposit flow routes
        Route::get('deposit-cancel/{id}', ['as' => 'deposit.cancel', 'uses' => 'WalletController@cancelDeposit']);
        Route::get('deposit-accept/{id}', ['as' => 'deposit.accept', 'uses' => 'WalletController@acceptDeposit']);

    });
});

