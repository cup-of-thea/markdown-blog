<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Illuminate\Support\Facades\DB;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;

class FillPostMetaCommand
{
    public function fill(int $postId, MarkdownPost $post): void
    {
        $readingTime = round(str($post->content)->wordCount() / 200);

        DB::table('post_meta')
            ->upsert([
                'post_id' => $postId,
                'reading_time' => $readingTime,
                'review_authors' => $post->meta->reviewAuthors,
                'created_at' => now(),
                'updated_at' => now(),
            ], ['post_id'], ['reading_time', 'review_authors', 'updated_at']);
    }
}
