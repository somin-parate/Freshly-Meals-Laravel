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

Route::get('version', function () {
    return response()->json(['version' => config('app.version')]);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});

Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::get('profile', 'ProfileController@profile');
    Route::put('profile', 'ProfileController@updateProfile');
    Route::post('change-password', 'ProfileController@changePassword');
    Route::get('timeslots', 'TimeslotController@loadTimeSlots');
    Route::get('planTypes', 'PlanController@loadPlanTypes');
    Route::get('allergensList', 'MealController@getAllergensName');
    Route::get('mealList', 'MealController@loadMeals');
    Route::get('getMacros', 'MealController@getMacros');
    Route::get('about_us', 'TermsConditionsController@aboutus');
    Route::get('terms_condition', 'TermsConditionsController@termsCondition');
    Route::get('privacy_policy', 'TermsConditionsController@privacyPolicy');

    // Timeslot Operations
    Route::get('slots', 'TimeslotController@slotsWithPaginate');
    Route::post('addSlot', 'TimeslotController@addSlot');
    Route::put('editSlot/{id}', 'TimeslotController@editSlot');
    Route::delete('deleteSlot/{id}', 'TimeslotController@deleteSlot');

    Route::get('freshlyUsers', 'FreshlyAuthController@freshlyUsers');
    Route::get('freshlyUsersWithoutPaginate', 'FreshlyAuthController@freshlyUsersWithoutPaginate');

    Route::get('foodItemsList', 'FoodItemController@getFoodItemsName');

    // user profile
    Route::get('freshlyUserProfile/{id}', 'FreshlyAuthController@freshlyUserProfile');
    Route::get('myActiveMeal/{user_id}', 'FreshlyAuthController@myActiveMeal');
    Route::get('myPastPlan/{user_id}', 'FreshlyAuthController@myPastPlan');
    Route::get('myUpcomingPlan/{user_id}', 'FreshlyAuthController@myUpcomingPlan');
    Route::put('updateMeal/{user_id}', 'FreshlyAuthController@updateMeal');
    Route::put('updateSnack/{user_id}', 'FreshlyAuthController@updateSnack');
    Route::get('freshlyUsersMeals/{user_id}', 'FreshlyAuthController@freshlyUsersMeals');

    // Kitchen
    Route::post('markCompleted', 'KitchenUserController@markCompletedMeal');
    Route::get('planShortcodes', 'KitchenUserController@planShortcodes');
    Route::get('getPreparationList', 'KitchenUserController@getPreparationList');
    Route::get('getPreparationListExportCsv', 'KitchenUserController@getPreparationListExportCsv');

    // Parcel
    Route::get('getParcelsList', 'ParcelUserController@getParcelsList');
    Route::get('getParcelsListExportCsv', 'ParcelUserController@getParcelsListExportCsv');

    // Parcel
    Route::get('getDeliveryList', 'ParcelUserController@getDeliveryList');
    Route::get('getDeliveryListExportCsv', 'ParcelUserController@getDeliveryListExportCsv');

    Route::get('removeCoverImage/{id}', 'PlanController@removeCoverImage');

    //bank requests
    Route::get('bankRequests', 'AllergensController@getBankRequestsList');
    Route::get('onlinePayments', 'AllergensController@onlinePayments');

    //feedbacks and refunds
    Route::get('feedback', 'FeedBackController@getFeedbackList');
    Route::get('faqs', 'FeedBackController@getFaqsList');
    Route::get('help_support', 'FeedBackController@getHelpSupportList');
    Route::get('general_issues', 'FeedBackController@getGeneralIssuesList');
    Route::post('storeFaqs', 'FeedBackController@storeFaqs');
    Route::post('editFaqs/{id}', 'FeedBackController@editFaqs');
    Route::get('deleteFaqs/{id}', 'FeedBackController@deleteFaqs');
    Route::get('getTypes', 'FeedBackController@getTypes');
    Route::get('bugs', 'FeedBackController@getBugsList');
    Route::get('refunds', 'FeedBackController@getRefundsList');
    Route::get('markPaidRefund/{id}', 'FeedBackController@markPaidRefund');
    Route::get('bankPayment/{id}', 'FeedBackController@bankPayment');
    Route::get('onlinePayment/{id}', 'FeedBackController@onlinePayment');
    Route::get('viewFeedback/{id}', 'FeedBackController@viewFeedback');
    Route::get('viewBug/{id}', 'FeedBackController@viewBug');
    Route::get('viewBagRefund/{id}', 'FeedBackController@viewBagRefund');
    Route::post('test', 'TestController@test');
    Route::get('notifications', 'FreshlyAuthController@adminNotifications');
    Route::post('/search-items', 'BaseController@itemsByTitle');
    Route::post('/search-city', 'BaseController@searchCities');
    Route::post('/search-plantypes', 'BaseController@searchPlanTypes');
    Route::post('/search-foods', 'BaseController@searchFoods');
    Route::post('/search-meals', 'BaseController@searchMeals');
    // Route::post('/search-Otransactions', 'BaseController@searchOnlineTransactions');
    // Route::post('/search-Btransactions', 'BaseController@searchBankTransactions');
    Route::get('/notificationByUserId/{user_id}', 'AllergensController@notificationByUserId');
    Route::get('/transactionByUserId/{user_id}', 'AllergensController@transactionByUserId');
    Route::post('/banners', 'BannerImagesController@bannersList');
    Route::get('bugByUserId/{id}', 'FeedBackController@bugByUserId');
    Route::get('feedbackByUserId/{id}', 'FeedBackController@feedbackByUserId');
    Route::get('getAddressForAdmin/{id}', 'AddressController@getAddressForAdmin');
    Route::post('deleteUser/{id}', 'FreshlyAuthController@deleteUser');
    Route::post('updateAdminUser/{id}', 'FreshlyAuthController@updateAdminUser');
    Route::get('loadAddress/{id}', 'AddressController@loadAddress');
    Route::post('addNotes/{id}/{notes}', 'FreshlyAuthController@addNotes');
    Route::get('getNotes/{id}', 'FreshlyAuthController@getNotes');


    Route::apiResources([
        'user'              => 'UserController',
        'plan'              => 'PlanController',
        'offers'            => 'OfferController',
        'food_items'        => 'FoodItemController',
        'cities'            => 'TimeslotController',
        'cityTimeslots'     => 'TimeslotController',
        'meal'              => 'MealController',
        'allergens'         => 'AllergensController',
        'banners'           => 'BannerImagesController',
        // 'faqs'              => 'FeedBackController',
    ]);

    Route::post('MealListDate', 'MealPlanController@MealsList');
    Route::post('MealListDateAdmin', 'MealPlanController@MealsListAdmin');
    Route::post('MealListByPlanAdmin', 'MealPlanController@MealListByPlanAdmin');
    Route::get('getAllPlanTypes', 'MealPlanController@getAllPlanTypes');
    Route::post('saveMealAdmin', 'MealPlanController@saveMealAdmin');
    Route::get('dashboardData', 'MealController@dashboardData');
    Route::post('bookNutritionistChecked/{id}', 'MealPlanController@bookNutritionistChecked');
});

//all events
Route::post('store-multiple-image', 'App\Http\Controllers\ImageController@store');