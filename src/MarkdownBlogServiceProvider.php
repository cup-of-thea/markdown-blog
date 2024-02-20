<?php

namespace CupOfThea\MarkdownBlog;

use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\LinkTaxonomiesCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\SynchronizeCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\UpsertPostCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\DuplicatedPostQuery;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\GetTagQuery;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\IndexTagsQuery;
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
        $this->app->bind(UpsertPostCommand::class, function (Application $app) {
            return new UpsertPostCommand();
        });
        $this->app->bind(LinkTaxonomiesCommand::class, function (Application $app) {
            return new LinkTaxonomiesCommand();
        });
        $this->app->bind(IndexTagsQuery::class, function (Application $app) {
            return new IndexTagsQuery();
        });
        $this->app->bind(GetTagQuery::class, function (Application $app) {
            return new GetTagQuery();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        if ($this->app->runningInConsole()) {
            $this->commands([
                SynchronizeCommand::class,
            ]);
        }
    }
}
