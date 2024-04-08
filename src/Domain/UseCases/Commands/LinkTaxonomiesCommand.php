<?php

namespace Thea\MarkdownBlog\Domain\UseCases\Commands;

use Illuminate\Support\Facades\DB;
use Thea\MarkdownBlog\Domain\ValueObjects\MarkdownPost;
use Thea\MarkdownBlog\Domain\ValueObjects\Tag;

class LinkTaxonomiesCommand
{
    public function link(int $postId, MarkdownPost $post): void
    {
        $this->linkCategory($post);
        $this->linkEdition($post);
        $this->linkTags($postId, $post);
        $this->linkSeries($postId, $post);
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('posts')
                ->where('slug', $post->meta->slug)
                ->update(['category_id' => $query->first()->id, 'updated_at' => now()]);
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
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $editionId = $query->first()->id;

            DB::table('posts')
                ->where('slug', $post->meta->slug)
                ->update(['edition_id' => $editionId, 'updated_at' => now()]);
        }
    }

    private function linkTags(int $postId, MarkdownPost $post): void
    {
        if (!empty($post->meta->tags)) {
            $tagIds = collect($post->meta->tags)
                ->map(fn($tag) => Tag::from($tag, str($tag)->slug()))
                ->map(fn(Tag $tag) => $this->getOrCreateTagId($tag))
                ->toArray();

            DB::table('post_tag')->upsert(
                collect($tagIds)
                    ->map(fn($tagId) => ['post_id' => $postId, 'tag_id' => $tagId, 'created_at' => now(), 'updated_at' => now()])
                    ->toArray(),
                ['post_id', 'tag_id'],
                ['updated_at']
            );
        }
    }

    private function getOrCreateTagId(Tag $tag)
    {
        $query = DB::table('tags')->where('slug', $tag->slug);

        $query->upsert([
            'title' => $tag->title,
            'slug' => $tag->slug,
            'created_at' => now(),
            'update_at' => now()
        ], ['slug'], ['title', 'updated_at']);

        return $query->first()->id;
    }

    private function linkSeries(int $postId, MarkdownPost $post): void
    {
        if (!empty($post->meta->series)) {
            $seriesSlug = str($post->meta->series)->slug();

            $query = DB::table('series')->where('slug', $seriesSlug);

            $query->upsert([
                'title' => $post->meta->series,
                'slug' => $seriesSlug,
                'created_at' => now(),
                'updated_at' => now(),
            ], ['slug'], ['title', 'updated_at']);

            $seriesId = $query->first()->id;

            DB::table('episodes')->upsert([
                'post_id' => $postId,
                'series_id' => $seriesId,
                'episode_number' => $post->meta->episode ?? '1',
                'created_at' => now(),
                'updated_at' => now(),
            ], ['post_id', 'series_id'], ['episode_number', 'updated_at']);
        }
    }

}
