<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::post('deviceRegistration', 'DeviceTokenController@deviceRegistration');
    Route::post('signup', 'FreshlyAuthController@register');
    Route::post('verify_email', 'FreshlyAuthController@verifyEmail');
    Route::post('resend_code', 'FreshlyAuthController@resendOtp');
    Route::post('signin', 'FreshlyAuthController@signIn');
    Route::post('forgotpassword', 'ForgotPasswordController@forgotPassword');
    Route::post('logout', 'FreshlyAuthController@Logout');
    Route::post('getprofile', 'FreshlyAuthController@getProfile');
    Route::post('editprofile', 'FreshlyAuthController@editprofile');
    Route::post('uploadimage', 'ImageUploadController@uploadImage');
    Route::post('getMealCategory', 'MealPlanController@getMealCategory');
    Route::post('getOffers', 'FreshlyAuthController@getOffers');
    Route::post('getCities', 'AddressController@getCities');
    Route::post('getTimeSlots', 'AddressController@getTimeSlots');
    Route::post('saveUserAddress', 'AddressController@saveUserAddress');
    Route::post('placeOrder', 'MealPlanController@addPlanToCart');
    Route::post('orderSummary', 'MealPlanController@orderSummary');
    Route::post('uploadProfilePic', 'FreshlyAuthController@uploadProfilePic');
    Route::post('myAddress', 'AddressController@myAddress');
    Route::post('editAddress', 'AddressController@editAddress');
    Route::post('removeAddress', 'AddressController@removeAddress');
    Route::post('mealsList', 'MealPlanController@mealsListCopy');
    Route::post('mealsListCopy', 'MealPlanController@mealsListCopy');
    Route::post('mealOrderSummary', 'MealPlanController@mealOrderSummary');
    Route::post('selectMeals', 'MealPlanController@selectMeals');
    Route::post('todaysMeals', 'KitchenUserController@getTodaysMeals');
    Route::post('getMealCategoryData', 'MealPlanController@getMealCategoryData');
    Route::post('couponApply', 'MealPlanController@couponApply');
    Route::post('payNow', 'MealPlanController@payNow');
    Route::post('changeAddress', 'MealPlanController@changeAddress');
    Route::post('myPlans', 'MealPlanController@myPlans');
    Route::post('enterCouponCode', 'AddressController@enterCouponCode');
    Route::post('finalPayPlan', 'MealPlanController@finalPayPlan');
    Route::post('getAddressById', 'AddressController@getAddressById');
    Route::post('myPlanPerDate', 'MealPlanController@myPlanPerDate');
    Route::post('editMeals', 'MealPlanController@editMeals');
    Route::post('deleteMeal', 'MealPlanController@deleteMeal');
    Route::post('socialLogin', 'FreshlyAuthController@socialLogin');
    Route::post('mySubscriptions', 'FreshlyAuthController@mySubscriptions');
    Route::post('pauseSubscription', 'FreshlyAuthController@pauseSubscription');
    Route::post('transactionHistory', 'FreshlyAuthController@transactionHistory');
    Route::post('notificationsList', 'FreshlyAuthController@notificationsList');
    Route::post('resubscribePlan', 'FreshlyAuthController@resubscribePlan');
    Route::post('storeFeedbacks', 'FeedBackController@storeFeedbacks');
    Route::post('getFaqs', 'FeedBackController@getFaqs');
    Route::post('pastOrders', 'FreshlyAuthController@pastOrders');
    Route::post('bagRefund', 'AddressController@bagRefund');
    Route::post('getAllAllergens', 'AllergensController@getAllAllergens');
    Route::post('getCustomMeal', 'FreshlyAuthController@getCustomMeal');
    Route::post('getFaqsNew', 'FreshlyAuthController@getFaqsNew');
    Route::post('getFaqsNewTest', 'FreshlyAuthController@getFaqsNewTest');
    Route::post('getBannerImages', 'BannerImagesController@getBannerImages');
    Route::post('resumeSubscription', 'FreshlyAuthController@resumeSubscription');
    Route::post('runningPlanSDate', 'MealPlanController@runningPlanSDate');
    Route::post('changeDefaultAddress', 'FreshlyAuthController@changeDefaultAddress');
    Route::post('getSelectedAddressId', 'FreshlyAuthController@getSelectedAddressId');
    Route::post('deleteNotification', 'FreshlyAuthController@deleteNotification');
    Route::post('notificationsReadCount', 'FreshlyAuthController@notificationsReadCount');
    Route::get('albums', 'AddressController@albums');
    Route::post('callback', 'FreshlyAuthController@callback')->name('callback');
    Route::post('importCsv', 'FoodItemController@importCsv');
});