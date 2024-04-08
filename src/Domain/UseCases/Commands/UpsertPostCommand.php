<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Illuminate\Support\Facades\DB;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;

class UpsertPostCommand
{
    public static function upsert(MarkdownPost $post): int
    {
        $query = DB::table('posts')->where('filePath', $post->filePath);

        $query->upsert(
            [...$post->toPostAttributes(), 'updated_at' => now(), 'created_at' => now()],
            'filePath',
            ['title', 'slug', 'description', 'content', 'date', 'canonical', 'updated_at']
        );

        return $query->first()->id;
    }
}
