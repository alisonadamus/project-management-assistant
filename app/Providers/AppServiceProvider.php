<?php

namespace Alison\ProjectManagementAssistant\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Alison\ProjectManagementAssistant\Services\MarkdownService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MarkdownService::class, function ($app) {
            return new MarkdownService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Використовувати Tailwind CSS для пагінації
        Paginator::useTailwind();
        URL::forceScheme('https');
    }
}
