<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Queries;

use Carbon\Carbon;
use Thea\MarkdownBlog\Domain\ValueObjects\Post;
use Illuminate\Support\Facades\DB;

class GetPostQuery
{
    public function get(string $slug): Post
    {
        $element = DB::table('posts')->where('slug', $slug)->first();

        return Post::from($element->title, $element->slug, $element->content, new Carbon($element->date));
    }
}
