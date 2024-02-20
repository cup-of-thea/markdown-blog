<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Queries;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\Post;
use Illuminate\Support\Facades\DB;

class GetPostQuery
{
    public function get(string $slug): Post
    {
        $element = DB::table('post')->where('slug', $slug)->first();

        return Post::from($element->title, $element->slug, $element->content, $element->date, $element->description);
    }
}
