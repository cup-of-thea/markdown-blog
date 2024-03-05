<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Illuminate\Support\Facades\DB;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Thea\MarkdownBlog\Domain\ValueObjects\Tag;

class LinkTaxonomiesCommand
{
    public function link(MarkdownPost $post): void
    {
        $this->linkCategory($post);
        $this->linkEdition($post);
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

    private function linkEdition(MarkdownPost $post): void
    {
        if (!empty($post->meta->edition)) {
            $editionSlug = str($post->meta->edition)->slug();

            $query = DB::table('editions')->where('slug', $editionSlug);

            $query->count() ?: $query->insert([
                'title' => $post->meta->edition,
                'slug' => $editionSlug,
            ]);

            $editionId = $query->first()->id;

            DB::table('posts')
                ->where('slug', $post->meta->slug)
                ->update(['edition_id' => $editionId]);
        }
    }
    private function linkTags(MarkdownPost $post): void
    {
        if (!empty($post->meta->tags)) {
            $tagIds = collect($post->meta->tags)
                ->map(fn($tag) => Tag::from($tag, str($tag)->slug()))
                ->map(fn(Tag $tag) => $this->getOrCreateTagId($tag))
                ->toArray();

            $postId = DB::table('posts')->where('slug', $post->meta->slug)->first()->id;

            DB::table('post_tag')->insertOrIgnore(
                collect($tagIds)
                    ->map(fn($tagId) => ['post_id' => $postId, 'tag_id' => $tagId])
                    ->toArray()
            );
        }
    }

    private function getOrCreateTagId(Tag $tag)
    {
        $query = DB::table('tags')->where('slug', $tag->slug);
        $query->count() ?: $query->insert(['title' => $tag->title, 'slug' => $tag->slug]);

        return $query->first()->id;
    }

}
