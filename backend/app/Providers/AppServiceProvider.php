<?php
namespace App\Providers;

use App\Models\Ticket;
use App\Policies\TicketPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::before(fn($user) => $user->hasRole("admin") ? true : null);
        RateLimiter::for(
            "api",
            fn(Request $request) => Limit::perMinute(90)->by(
                $request->user()?->id ?: $request->ip(),
            ),
        );
        RateLimiter::for(
            "auth",
            fn(Request $request) => Limit::perMinute(5)->by($request->ip()),
        );
    }
}
