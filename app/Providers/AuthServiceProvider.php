<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        /**
         * Defining the user Roles
         */
        Gate::define('isAdmin', function ($user) {
            // if ($user->isAdmin()) {
            //     return true;
            // }

            // for simplicity
            return $user->type === 'admin';
        });

        Gate::define('isUser', function ($user) {
            return $user->type === 'user';
        });

        Gate::define('isKithcenUser', function ($user) {
            return $user->type === 'kithcen';
        });

        Gate::define('isFinanceUser', function ($user) {
            return $user->type === 'finance';
        });

        Gate::define('isCustomerMgmt', function ($user) {
            return $user->type === 'customer-mgmt';
        });

        Gate::define('isParcelUser', function ($user) {
            return $user->type === 'parcel';
        });
    }
}
