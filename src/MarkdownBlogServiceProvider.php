<?php

namespace CupOfThea\MarkdownBlog;

use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\LinkTaxonomiesCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\SynchronizeCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\UpsertPostCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\DuplicatedPostQuery;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\GetCategoryFromPostQuery;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\GetPostQuery;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\GetTagQuery;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\GetTagsFromPost;
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
        $this
            ->scoped(UpsertPostCommand::class)
            ->scoped(LinkTaxonomiesCommand::class)

            ->scoped(DuplicatedPostQuery::class)
            ->scoped(IndexTagsQuery::class)
            ->scoped(GetTagQuery::class)
            ->scoped(GetCategoryFromPostQuery::class)
            ->scoped(GetPostQuery::class)
            ->scoped(GetTagsFromPost::class)
        ;
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

    private function scoped(string $class): self
    {
        $this->app->scoped($class, fn() => new $class());

        return $this;
    }
}
