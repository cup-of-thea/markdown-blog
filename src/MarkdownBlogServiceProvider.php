<?php

namespace CupOfThea\MarkdownBlog;

use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\SynchronizeCommand;
use Illuminate\Support\ServiceProvider;

class MarkdownBlogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        if ($this->app->runningInConsole()) {
            $this->commands([
                SynchronizeCommand::class,
            ]);
        }
    }
}
