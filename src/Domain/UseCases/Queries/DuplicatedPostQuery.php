<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Queries;

use Illuminate\Support\Facades\DB;

class DuplicatedPostQuery
{
    public function check(string $slug, string $filepath): ?string
    {
        return DB::table('posts')
            ->where('slug', $slug)
            ->where('filePath', '!=', $filepath)
            ->first()?->filePath;
    }
}
