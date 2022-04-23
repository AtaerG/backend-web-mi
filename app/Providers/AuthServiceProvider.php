<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use App\Policies\CommentPolicy;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
          Comment::class => CommentPolicy::class,
          Order::class => OrderPolicy::class,
          User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies();
        Passport::routes();
        //
        Passport::tokensCan([
            'admin' => 'Could do everything',
            'normal_user' => 'Cant add, edit, delete products'
        ]);

        Passport::setDefaultScope([
            'basic'
        ]);

        Gate::define('isAdmin', function($user) {
            return $user->role == 'admin';
        });

        Gate::define('isNormalUser', function($user) {
            return $user->role == 'normal_user';
        });

        Gate::define('isUsers', function(User $user,$item) {
            return $item->user_id == $user->id;
        });

        $this->registerPolicies($gate);
    }
}
