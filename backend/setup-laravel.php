<?php

/**
 * Laravel Setup Script
 * This script creates all necessary Laravel files for AS Hub backend
 */

$files = [
    'app/Http/Middleware/TrimStrings.php' => '<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\TrimStrings as Middleware;

class TrimStrings extends Middleware
{
    protected $except = [
        \'current_password\',
        \'password\',
        \'password_confirmation\',
    ];
}',

    'app/Http/Middleware/EncryptCookies.php' => '<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    protected $except = [
        //
    ];
}',

    'app/Http/Middleware/VerifyCsrfToken.php' => '<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        //
    ];
}',

    'app/Http/Middleware/Authenticate.php' => '<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route(\'login\');
    }
}',

    'app/Http/Middleware/RedirectIfAuthenticated.php' => '<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect(RouteServiceProvider::HOME);
            }
        }

        return $next($request);
    }
}',

    'app/Http/Middleware/ValidateSignature.php' => '<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ValidateSignature as Middleware;

class ValidateSignature extends Middleware
{
    protected $except = [
        //
    ];
}',

    'app/Providers/RouteServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = \'/home\';

    public function boot(): void
    {
        RateLimiter::for(\'api\', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware(\'api\')
                ->prefix(\'api\')
                ->group(base_path(\'routes/api.php\'));

            Route::middleware(\'web\')
                ->group(base_path(\'routes/web.php\'));
        });
    }
}',

    'app/Providers/AppServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        //
    }
}',

    'app/Providers/AuthServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        //
    }
}',

    'app/Providers/EventServiceProvider.php' => '<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}',

    'config/app.php' => '<?php

return [
    \'name\' => env(\'APP_NAME\', \'AS Hub\'),
    \'env\' => env(\'APP_ENV\', \'production\'),
    \'debug\' => (bool) env(\'APP_DEBUG\', false),
    \'url\' => env(\'APP_URL\', \'http://localhost\'),
    \'asset_url\' => env(\'ASSET_URL\'),
    \'timezone\' => \'UTC\',
    \'locale\' => \'en\',
    \'fallback_locale\' => \'en\',
    \'faker_locale\' => \'en_US\',
    \'key\' => env(\'APP_KEY\'),
    \'cipher\' => \'AES-256-CBC\'),
    \'maintenance\' => [
        \'driver\' => \'file\',
    ],
    \'providers\' => [
        Illuminate\Auth\AuthServiceProvider::class,
        Illuminate\Broadcasting\BroadcastServiceProvider::class,
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Mail\MailServiceProvider::class,
        Illuminate\Notifications\NotificationServiceProvider::class,
        Illuminate\Pagination\PaginationServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Auth\Passwords\PasswordResetServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ],
    \'aliases\' => [],
];',
];

echo "Creating Laravel files...\n";

foreach ($files as $path => $content) {
    $fullPath = __DIR__ . '/' . $path; // ← هذا هو التصحيح
    $dir = dirname($fullPath);

    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "Created directory: $dir\n";
    }

    file_put_contents($fullPath, $content);
    echo "Created file: $path\n";
}


echo "\nAll files created successfully!\n";
echo "Run: php artisan --version to verify installation\n";
