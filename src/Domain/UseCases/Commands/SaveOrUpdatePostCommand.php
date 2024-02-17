<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Illuminate\Support\Facades\DB;

class SaveOrUpdatePostCommand
{
    public static function saveOrUpdate(MarkdownPost $post): void
    {
        $query = DB::table('posts')->where('filePath', $post->filePath);

        $query->count()
            ? $query->update($post->toPostAttributes())
            : $query->insert($post->toPostAttributes());
    }
}
