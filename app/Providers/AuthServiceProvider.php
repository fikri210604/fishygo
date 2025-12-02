<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate dinamis berbasis permission slug
        Gate::before(function (User $user, string $ability) {
            if ($user->isAdmin()) {
                return true;
            }
            // Jika user memiliki permission bernama $ability, izinkan
            return $user->hasPermission($ability) ? true : null;
        });

        Gate::define('access-admin', function (User $user) {
            return $user->hasRole(User::ROLE_ADMIN);
        });

        Gate::define('access-kurir', function (User $user) {
            return $user->hasRole(User::ROLE_KURIR);
        });

        Gate::define('access-user', function (User $user) {
            return $user->hasRole(User::ROLE_USER);
        });

        // Custom desain email reset password
        ResetPassword::toMailUsing(function ($notifiable, string $token) {
            $email = $notifiable->getEmailForPasswordReset();
            $url = route('password.reset', ['token' => $token, 'email' => $email]);
            $passwordBroker = config('auth.defaults.passwords');
            $expire = (int) config("auth.passwords.$passwordBroker.expire", 60);
            $appName = config('app.name', 'Aplikasi');

            return (new MailMessage)
                ->subject("Reset Password $appName")
                ->markdown('emails.auth.reset-password', [
                    'url' => $url,
                    'expire' => $expire,
                    'appName' => $appName,
                    'supportEmail' => config('mail.from.address'),
                    'userEmail' => $email,
                ]);
        });
    }
}
