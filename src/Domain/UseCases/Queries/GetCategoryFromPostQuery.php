<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Queries;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\Category;
use CupOfThea\MarkdownBlog\Domain\ValueObjects\Post;
use Illuminate\Support\Facades\DB;

class GetCategoryFromPostQuery
{
    public function get(Post $post): Category
    {
        $element = DB::table('categories as c')
            ->select('c.title', 'c.slug')
            ->join('posts as p', 'c.id', '=', 'p.category_id')
            ->where('p.slug', $post->slug)
            ->first();

        return Category::from($element->title, $element->slug);
    }
}
