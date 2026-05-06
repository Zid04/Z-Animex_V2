<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Media;
use App\Models\Season;
use App\Models\Tag;
use App\Models\UserFavorite;
use App\Models\UserMedia;
use App\Models\UserRating;
use App\Policies\CommentPolicy;
use App\Policies\EpisodePolicy;
use App\Policies\MediaPolicy;
use App\Policies\SeasonPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserFavoritePolicy;
use App\Policies\UserMediaPolicy;
use App\Policies\UserRatingPolicy;
use Carbon\CarbonImmutable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerPolicies();
        JsonResource::withoutWrapping();
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function registerPolicies(): void
    {
        Gate::policy(Comment::class,      CommentPolicy::class);
        Gate::policy(Episode::class,      EpisodePolicy::class);
        Gate::policy(Media::class,        MediaPolicy::class);
        Gate::policy(Season::class,       SeasonPolicy::class);
        Gate::policy(Tag::class,          TagPolicy::class);
        Gate::policy(UserFavorite::class, UserFavoritePolicy::class);
        Gate::policy(UserMedia::class,    UserMediaPolicy::class);
        Gate::policy(UserRating::class,   UserRatingPolicy::class);
    }
}