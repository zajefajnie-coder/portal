<?php

namespace App\Providers;

use App\Models\Casting;
use App\Models\Portfolio;
use App\Policies\CastingPolicy;
use App\Policies\PortfolioPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Portfolio::class => PortfolioPolicy::class,
        Casting::class => CastingPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}


