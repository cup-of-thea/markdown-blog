<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer;

use CupOfThea\MarkdownBlog\Exceptions\SlugIsAlreadyTakenException;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Yaml;

class SynchronizeCommand extends Command
{
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
     */
    public function handle()
    {
        $this->info('Post created successfully.');

        collect(Storage::allFiles('posts'))->each(function (string $path) {
            $this->generate(Storage::get($path), $path);
        });

        return Command::SUCCESS;
    }

    /**
     * @throws SlugIsAlreadyTakenException
     */
    public static function generate(string $content, string $path): void
    {
        // @todo: refactor this into pipeline
        $meta = Yaml::parse(str($content)->after('---')->before('---')->trim()->toString());
        $post = MarkdownPost::parse($content, $path, $meta);
        self::ensurePostNotDuplicated($post);
        $savedPost = self::saveOrUpdate($post);
        self::linkTaxonomies($meta, $savedPost);
    }

    public static function ensurePostNotDuplicated(MarkdownPost $post): void
    {
        if ($originalPost = Post::where('slug', $post->slug)->where('filePath', '!=', $post->filePath)->first()) {
            throw new SlugIsAlreadyTakenException($post->slug, $originalPost->filePath, $post->filePath);
        }
    }

    public static function saveOrUpdate(MarkdownPost $post): Post
    {
        if ($originalPost = Post::where('filePath', $post->filePath)->first()) {
            $originalPost->update($post->toPostAttributes());

            return $originalPost->refresh();
        }

        return Post::create($post->toPostAttributes());
    }


    public static function linkTaxonomies(mixed $meta, Post $post)
    {
        if (isset($meta['category'])) {
            $categorySlug = str($meta['category'])->slug();
            $category =
                Category::where('slug', $categorySlug)->first()
                    ?: Category::create(['title' => $meta['category'], 'slug' => $categorySlug]);

            $post->category()->associate($category)->save();
        }
    }
}
