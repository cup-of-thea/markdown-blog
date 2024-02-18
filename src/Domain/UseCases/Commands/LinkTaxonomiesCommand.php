<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands;

use CupOfThea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Illuminate\Support\Facades\DB;

class LinkTaxonomiesCommand
{
    public function link(MarkdownPost $post): void
    {
        $this->linkCategory($post);
        $this->linkTags($post);
    }

    /**
     * @param MarkdownPost $post
     * @return void
     */
    private function linkCategory(MarkdownPost $post): void
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

    private function linkTags(MarkdownPost $post): void
    {
        if (!empty($post->meta->tags)) {
            $tagIds = collect($post->meta->tags)
                ->map(fn($tag) => str($tag)->slug())
                ->map(fn($tag) => $this->findOrCreateTag($tag))
                ->toArray();

            $postId = DB::table('posts')->where('slug', $post->meta->slug)->first()->id;

            DB::table('post_tag')->where('post_id', $postId)->delete();

            DB::table('post_tag')->insert(
                collect($tagIds)
                    ->map(fn($tagId) => ['post_id' => $postId, 'tag_id' => $tagId])
                    ->toArray()
            );
        }
    }

    private function findOrCreateTag($tag)
    {
        $query = DB::table('tags')->where('slug', $tag);

        $query->count() ?: $query->insert([
            'title' => $tag,
            'slug' => $tag,
        ]);

        return $query->first()->id;
    }
}
