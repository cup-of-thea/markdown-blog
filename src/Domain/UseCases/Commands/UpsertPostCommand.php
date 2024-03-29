<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Illuminate\Support\Facades\DB;

class UpsertPostCommand
{
    public static function upsert(MarkdownPost $post): void
    {
        $query = DB::table('posts')->where('filePath', $post->filePath);

        $query->count()
            ? $query->update($post->toPostAttributes())
            : $query->insert($post->toPostAttributes());
    }
}
