<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Queries;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\Category;
use CupOfThea\MarkdownBlog\Domain\ValueObjects\Post;
use Illuminate\Support\Facades\DB;

class GetCategoryFromPostQuery
{
    public function get(Post $post): Category
    {
        $element = DB::table('categories')
            ->select('title', 'slug')
            ->join('posts', 'categories.id', '=', 'posts.category_id')
            ->where('posts.slug', $post->slug)
            ->first();

        return Category::from($element->title, $element->slug);
    }
}
