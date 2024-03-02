<?php

namespace Thea\MarkdownBlog;

use Thea\MarkdownBlog\Domain\UseCases\Commands\LinkTaxonomiesCommand;
use Thea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\SynchronizeCommand;
use Thea\MarkdownBlog\Domain\UseCases\Commands\UpsertPostCommand;
use Thea\MarkdownBlog\Domain\UseCases\Queries\DuplicatedPostQuery;
use Thea\MarkdownBlog\Domain\UseCases\Queries\GetCategoryFromPostQuery;
use Thea\MarkdownBlog\Domain\UseCases\Queries\GetPostQuery;
use Thea\MarkdownBlog\Domain\UseCases\Queries\GetTagQuery;
use Thea\MarkdownBlog\Domain\UseCases\Queries\GetTagsFromPost;
use Thea\MarkdownBlog\Domain\UseCases\Queries\IndexTagsQuery;
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
