<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Queries;

use Thea\MarkdownBlog\Domain\ValueObjects\Post;
use Thea\MarkdownBlog\Domain\ValueObjects\Tag;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetTagsFromPost
{
    public function get(Post $post): Collection
    {
        return DB::table('tags as t')
            ->select('t.title', 't.slug')
            ->join('post_tag as pt', 't.id', '=', 'pt.tag_id')
            ->join('posts as p', 'pt.post_id', '=', 'p.id')
            ->where('p.slug', $post->slug)
            ->get()
            ->map(fn($element) => Tag::from($element->title, $element->slug));
    }
}
