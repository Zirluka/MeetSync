<?php

namespace App\Providers;

use App\Repositories\Contracts\AvailabilityRepositoryInterface;
use App\Repositories\Contracts\MeetingRepositoryInterface;
use App\Repositories\Contracts\ParticipantRepositoryInterface;
use App\Repositories\Contracts\SuggestedSlotRepositoryInterface;
use App\Repositories\Eloquent\AvailabilityRepository;
use App\Repositories\Eloquent\MeetingRepository;
use App\Repositories\Eloquent\ParticipantRepository;
use App\Repositories\Eloquent\SuggestedSlotRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MeetingRepositoryInterface::class, MeetingRepository::class);
        $this->app->bind(ParticipantRepositoryInterface::class, ParticipantRepository::class);
        $this->app->bind(AvailabilityRepositoryInterface::class, AvailabilityRepository::class);
        $this->app->bind(SuggestedSlotRepositoryInterface::class, SuggestedSlotRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
