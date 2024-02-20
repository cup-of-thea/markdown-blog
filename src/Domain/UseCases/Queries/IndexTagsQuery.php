<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Queries;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\Tag;
use Illuminate\Support\Facades\DB;

class IndexTagsQuery
{
    public function index(): array
    {
        $collection = DB::table('tags as t')
            ->select(DB::raw('t.title, t.slug, COUNT(pt.id) as postCount'))
            ->leftJoin('post_tag as pt', 't.id', '=', 'pt.tag_id')
            ->groupBy('t.title', 't.slug')
            ->orderBy('t.title')
            ->get();

        return $collection->map(fn($element) => Tag::from(
            $element->title,
            $element->slug,
            $element->postCount
        ))->toArray();
    }
}
