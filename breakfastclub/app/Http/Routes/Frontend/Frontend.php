<?php

/**
 * Frontend Controllers
 */
Route::get('/', 'FrontendController@index')->name('frontend.index');
//Route::get('macros', 'FrontendController@macros')->name('frontend.macros');

/**
 * These frontend controllers require the user to be logged in
 */
    Route::group(['middleware' => 'auth'], function () {
    Route::group(['namespace' => 'User'], function() {

        Route::get('dashboard', 'DashboardController@index')->name('frontend.user.dashboard');
        //Route::get('profile/edit', 'ProfileController@edit')->name('frontend.user.profile.edit');
        //Route::patch('profile/update', 'ProfileController@update')->name('frontend.user.profile.update');//remove this
        Route::patch('/update_avatar/{userID}', 'FrontUserController@update_avatar');

        /**
         * These routes handle Events'/Equipments' details display
         */

        Route::get('Events', 'EventsController@getAll')->name('frontend.events.getall');//just trying to name routes
        Route::get('Event/{eventID}', 'EventsController@find_by_id');
        
        route::get('Items' , 'ItemsController@getAll');
        Route::get('Item/{name}', 'ItemsController@find_by_name');
        Route::get('Item/{name}/{itemID}', 'ItemsController@find_by_id');

        /*Route::get('Item/{name}/{itemID}/Edit', 'ItemsController@edit');
        Route::patch('Category/{categoryID}/Update', 'CategoriesController@update');*/
        
        route::get('Categories' , 'CategoriesController@getAll');
        route::get('Category/{categoryID}' , 'CategoriesController@find_by_id');

        /*Equipements management inside events */
        Route::post('closeEvent/{eventID}', 'EventsController@close');
        Route::post('Event/ItemRelease/{ID}', 'EventsController@itemRelease');
        Route::post('Event/ItemReserve/{ID}', 'EventsController@itemReserve');
        Route::post('Event/ItemRelease/Multi/{ID}', 'EventsController@itemReleaseMulti');
        Route::post('Event/ItemReserve/Multi/{ID}', 'EventsController@itemReserveMulti');

        
        /**
         * This Middleware manages access/management of users and their details
         */

        Route::group(['middleware' => 'access.routeNeedsRole:Administrateur'], function()
        {

            /*Users' Management*/

            /**
             * These routes handle all the User Management actions
             */

            Route::get('Users', 'FrontUserController@getAll');
            Route::get('User/{userID}', 'FrontUserController@find_by_id');
            Route::get('User/{userID}/Edit', 'FrontUserController@edit');
            Route::post('createUser', 'FrontUserController@store');
            Route::get('email_check/{email}', 'FrontUserController@email_check');
            Route::patch('User/{userID}/Update', 'FrontUserController@update');
            Route::delete('User/{userID}/Delete', 'FrontUserController@destroy');
            Route::patch('/User/{userID}/updatePassword', 'FrontUserController@updatePassword');



        });

        /**
         * This Middleware manages access to events management
         */

        Route::group(['middleware' => 'access.routeNeedsPermission:manage-events'], function()
        {

            /*Event Management*/
            /**
             * These routes handle all the Events' actions
             */

            Route::get('Event/{eventID}/Edit', 'EventsController@edit');
            Route::post('createEvent', 'EventsController@store');
            Route::patch('Event/{eventID}/Update', 'EventsController@update');
            Route::delete('Event/{eventID}/Delete', 'EventsController@destroy');


            /*Orders PDFs Generation*/
            /**
             * This route is for order pdfs generation
             */

            Route::post('pdf', 'EventsController@PDFGen');

        });

        /**
         * This Middleware manages access to items pdf Generation
         */

        Route::group(['middleware' => 'access.routeNeedsPermission:manage-items'], function()
        {

            /*Equipements PDFs Generation */

            Route::post('itempdf', 'EventsController@PDFEquipementsGen');

            /*Equipements management */

            Route::get('/Item/{itemID}/Edit', 'ItemsController@edit');
            Route::post('createItem', 'ItemsController@store');
            Route::patch('Item/{itemID}/Update', 'ItemsController@update');
            Route::delete('Item/{itemID}/Delete', 'ItemsController@destroy');
            
            Route::patch('update_item_image/{itemID}', 'ItemsController@update_item_image');
            Route::post('createCategory', 'CategoriesController@store');
            Route::get('Category/{categoryID}/Edit', 'CategoriesController@edit');
            Route::patch('Category/{categoryID}/Update', 'CategoriesController@update');
            Route::delete('Category/{categoryID}/Delete', 'CategoriesController@destroy');


        });


    });
});