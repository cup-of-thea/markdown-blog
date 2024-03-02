<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Queries;

use Thea\MarkdownBlog\Domain\ValueObjects\Tag;
use Illuminate\Support\Facades\DB;

class GetTagQuery
{
    public function get(string $slug): Tag
    {
        $element = DB::table('tags')->where('slug', $slug)->first();
        $postCount = DB::table('post_tag')->where('tag_id', $element->id)->count();

        return Tag::from($element->title, $element->slug, $postCount);
    }
}
