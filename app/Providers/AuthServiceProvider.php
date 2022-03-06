<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use App\Policies\CommentPolicy;
use Laravel\Passport\Passport;

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

        $this->registerPolicies($gate);
    }
}
