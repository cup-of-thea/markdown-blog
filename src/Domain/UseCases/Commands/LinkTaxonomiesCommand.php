<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Illuminate\Support\Facades\DB;

class LinkTaxonomiesCommand
{
    public function link(MarkdownPost $post): void
    {
        $this->linkCategory($post);
    }

    /**
     * @param MarkdownPost $post
     * @return void
     */
    public function linkCategory(MarkdownPost $post): void
    {
        if (!empty($post->meta->category)) {
            $categorySlug = str($post->meta->category)->slug();

            $query = DB::table('categories')->where('slug', $categorySlug);

            $query->count() ?: $query->insert([
                'title' => $post->meta->category,
                'slug' => $categorySlug,
            ]);

            $categoryId = $query->first()->id;

            DB::table('posts')
                ->where('slug', $post->meta->slug)
                ->update(['category_id' => $categoryId]);
        }
    }
}
