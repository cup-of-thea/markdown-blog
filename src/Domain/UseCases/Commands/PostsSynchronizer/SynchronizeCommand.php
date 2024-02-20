<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer;

use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\LinkTaxonomiesCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\UpsertPostCommand;
use CupOfThea\MarkdownBlog\Domain\UseCases\Queries\DuplicatedPostQuery;
use CupOfThea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use CupOfThea\MarkdownBlog\Exceptions\SlugIsAlreadyTakenException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SynchronizeCommand extends Command
{
    public function __construct(
        private readonly DuplicatedPostQuery   $duplicatedPostQuery,
        private readonly UpsertPostCommand     $upsertPostCommand,
        private readonly LinkTaxonomiesCommand $linkTaxonomiesCommand,
    )
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:synchronize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize posts from storage to database.';

    /**
     * Execute the console command.
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
        $this->upsertPostCommand->upsert($post);
        $this->linkTaxonomiesCommand->link($post);
    }
}
