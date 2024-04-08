<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Illuminate\Support\Facades\DB;
use Thea\MarkdownBlog\Domain\ValueObjects\Author;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;

class LinkAuthorsCommand
{
    public function link(MarkdownPost $post): void
    {
        $this->linkAuthors($post);
    }

    private function linkAuthors(MarkdownPost $post): void
    {
        if (!empty($post->meta->authors)) {
            $authorIds = collect($post->meta->authors)
                ->map(fn($author) => Author::from($author, str($author)->slug()))
                ->map(fn(Author $author) => $this->getOrCreateAuthorId($author))
                ->toArray();

            $postId = DB::table('posts')->where('slug', $post->meta->slug)->first()->id;

            DB::table('post_author')->insertOrIgnore(
                collect($authorIds)
                    ->map(fn($authorId) => ['post_id' => $postId, 'author_id' => $authorId])
                    ->toArray()
            );
        }
    }

    private function getOrCreateAuthorId(Author $tag)
    {
        $query = DB::table('authors')->where('slug', $tag->slug);
        $query->count() ?: $query->insert(['name' => $tag->name, 'slug' => $tag->slug]);

        return $query->first()->id;
    }
}
