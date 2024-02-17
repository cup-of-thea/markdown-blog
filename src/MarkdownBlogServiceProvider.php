<?php

namespace CupOfThea\MarkdownBlog;

use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\LinkTaxonomiesCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\SynchronizeCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\SaveOrUpdatePostCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\DuplicatedPostQuery;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MarkdownBlogServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(DuplicatedPostQuery::class, function (Application $app) {
            return new DuplicatedPostQuery();
        });
        $this->app->bind(SaveOrUpdatePostCommand::class, function (Application $app) {
            return new SaveOrUpdatePostCommand();
        });
        $this->app->bind(LinkTaxonomiesCommand::class, function (Application $app) {
            return new LinkTaxonomiesCommand();
        });
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
