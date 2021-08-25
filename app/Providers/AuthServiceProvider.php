<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use App\Models\UserIntention;
use App\Models\Resume;
use App\Policies\UserIntentionPolicy;
use App\Policies\ResumePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        UserIntention::class => UserIntentionPolicy::class,
        Resume::class => ResumePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();

            Passport::$ignoreCsrfToken = true;

            Passport::tokensExpireIn(now()->addDays(15));

            Passport::refreshTokensExpireIn(now()->addDays(30));

            Passport::personalAccessTokensExpireIn(now()->addMonths(6));
        }

        Gate::define('signed', function ($user) {
            return $user->is_signup;
        });
    }
}
