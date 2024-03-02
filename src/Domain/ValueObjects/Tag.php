<?php

namespace Thea\MarkdownBlog\Domain\ValueObjects;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Wireable;

readonly class Tag implements Wireable
{
    private function __construct(
        public string $title,
        public string $slug,
        public int    $postCount
    )
    {
    }

    public static function from(string $title, string $slug, int $postCount = 0): Tag
    {
        return new self($title, $slug, $postCount);
    }

    public function posts(): Collection
    {
        return DB::table('posts')
            ->select('posts.title', 'posts.slug', 'posts.date', 'categories.title as category')
            ->join('post_tag', 'posts.id', '=', 'post_tag.post_id')
            ->join('tags', 'post_tag.tag_id', '=', 'tags.id')
            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
            ->where('tags.slug', $this->slug)
            ->orderBy('date', 'desc')
            ->get()
            ->map(fn($post) => PostLine::from(
                $post->title,
                $post->slug,
                $post->date,
                $post->category
            ));
    }

    public function toLivewire(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'postCount' => $this->postCount,
        ];
    }

    public static function fromLivewire($value): Tag
    {
        return new self(
            $value->title,
            $value->slug,
            $value->postCount
        );
    }
}
