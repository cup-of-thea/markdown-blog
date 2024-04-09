<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Thea\MarkdownBlog\Domain\UseCases\Commands\FillPostMetaCommand;
use Thea\MarkdownBlog\Domain\UseCases\Commands\LinkAuthorsCommand;
use Thea\MarkdownBlog\Domain\UseCases\Commands\LinkTaxonomiesCommand;
use Thea\MarkdownBlog\Domain\UseCases\Commands\UpsertPostCommand;
use Thea\MarkdownBlog\Domain\UseCases\Queries\DuplicatedPostQuery;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Thea\MarkdownBlog\Exceptions\MissingPostDateException;
use Thea\MarkdownBlog\Exceptions\MissingPostTitleException;
use Thea\MarkdownBlog\Exceptions\SlugIsAlreadyTakenException;

class SynchronizeCommand extends Command
{
    public function __construct(
        private readonly DuplicatedPostQuery   $duplicatedPostQuery,
        private readonly UpsertPostCommand     $upsertPostCommand,
        private readonly LinkTaxonomiesCommand $linkTaxonomiesCommand,
        private readonly LinkAuthorsCommand    $linkAuthorsCommand,
        private readonly FillPostMetaCommand   $fillPostMetaCommand
    )
    {
        parent::__construct();
    }

    protected $signature = 'app:synchronize';

    protected $description = 'Synchronize posts from storage to database.';

    /**
     * Execute the console command.
     * @throws MissingPostDateException
     * @throws MissingPostTitleException
     * @throws SlugIsAlreadyTakenException
     */
    public function handle(): int
    {
        $this->info('Starting.');

        collect(Storage::allFiles('posts'))->each(function (string $path) {
            $this->generatePost(Storage::get($path), $path);
        });

        $this->info('Posts synchronized successfully.');

        return Command::SUCCESS;
    }

    /**
     * @throws SlugIsAlreadyTakenException
     * @throws MissingPostDateException
     * @throws MissingPostTitleException
     */
    public function generatePost(string $content, string $path): void
    {
        $this->commit(MarkdownPost::parse($content, $path));
    }

    /**
     * @throws SlugIsAlreadyTakenException
     */
    private function ensurePostNotDuplicated(MarkdownPost $post): void
    {
        if ($originalFilePath = $this->duplicatedPostQuery->check($post->meta->slug, $post->filePath)) {
            throw new SlugIsAlreadyTakenException($post->meta->slug, $originalFilePath, $post->filePath);
        }
    }

    /**
     * @throws SlugIsAlreadyTakenException
     */
    private function commit(MarkdownPost $post): void
    {
        $this->ensurePostNotDuplicated($post);
        $postId = $this->upsertPostCommand->upsert($post);
        $this->linkTaxonomiesCommand->link($postId, $post);
        $this->linkAuthorsCommand->link($postId, $post);
        $this->fillPostMetaCommand->fill($postId, $post);
    }
}
