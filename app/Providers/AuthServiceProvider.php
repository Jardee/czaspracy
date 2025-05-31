<?php

namespace App\Providers;

use App\Models\User;
use App\Models\WorkEntry;
use App\Models\Comment;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('create-work-entry', function (User $user) {
            return $user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR;
        });

        Gate::define('view-work-entry', function (User $user, WorkEntry $workEntry) {
            if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR) {
                return true;
            }
            return $user->id === $workEntry->user_id;
        });

        Gate::define('update-work-entry', function (User $user, WorkEntry $workEntry) {
            return $user->role === User::ROLE_ADMIN || ($user->role === User::ROLE_SUPERVISOR);
        });

        Gate::define('delete-work-entry', function (User $user, WorkEntry $workEntry) {
            return $user->role === User::ROLE_ADMIN || ($user->role === User::ROLE_SUPERVISOR);
        });

        Gate::define('add-comment', function (User $user, WorkEntry $workEntry) {
            if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR) {
                return true;
            }
            if ($user->role === User::ROLE_EMPLOYEE && $user->id === $workEntry->user_id) {
                return true;
            }
            return false;
        });

        Gate::define('update-comment', function (User $user, Comment $comment) {
            if ($user->role === User::ROLE_ADMIN) {
                return true;
            }
            return $user->id === $comment->user_id;
        });

        Gate::define('delete-comment', function (User $user, Comment $comment) {
            if ($user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR) {
                return true;
            }
            return $user->id === $comment->user_id;
        });

        Gate::define('view-all-work-entries', function(User $user) {
            return $user->role === User::ROLE_ADMIN || $user->role === User::ROLE_SUPERVISOR;
        });

        Gate::define('manage-users', function(User $user) {
            return $user->role === User::ROLE_ADMIN;
        });
    }
}