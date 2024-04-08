<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Illuminate\Support\Facades\DB;
use Thea\MarkdownBlog\Domain\ValueObjects\Author;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;

class LinkAuthorsCommand
{
    public function link(int $postId, MarkdownPost $post): void
    {
        $this->linkAuthors($postId, $post);
    }

    private function linkAuthors(int $postId, MarkdownPost $post): void
    {
        if (!empty($post->meta->authors)) {
            $authorIds = collect($post->meta->authors)
                ->map(fn($author) => Author::from($author, str($author)->slug()))
                ->map(fn(Author $author) => $this->getOrCreateAuthorId($author))
                ->toArray();

            DB::table('post_author')->upsert(
                collect($authorIds)
                    ->map(fn($authorId) => [
                        'post_id' => $postId,
                        'author_id' => $authorId,
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                    ->toArray(),
                ['post_id', 'author_id'],
                ['updated_at']
            );
        }
    }

    private function getOrCreateAuthorId(Author $tag)
    {
        $query = DB::table('authors')->where('slug', $tag->slug);
        $query->upsert([
            'name' => $tag->name,
            'slug' => $tag->slug,
            'created_at' => now(),
            'update_at' => now()
        ], 'slug', ['name', 'slug', 'update_at']);

        return $query->first()->id;
    }
}
