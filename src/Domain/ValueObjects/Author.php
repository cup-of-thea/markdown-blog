<?php

namespace Thea\MarkdownBlog\Domain\ValueObjects;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Wireable;

readonly class Author implements Wireable
{
    private function __construct(
        public string $name,
        public string $slug,
        public int    $postCount
    )
    {
    }

    public static function from(string $title, string $slug, int $postCount = 0): Author
    {
        return new self($title, $slug, $postCount);
    }

    public function posts(): Collection
    {
        return DB::table('posts')
            ->select('posts.title', 'posts.slug', 'posts.date', 'categories.title as category')
            ->join('post_author', 'posts.id', '=', 'post_author.post_id')
            ->join('authors', 'post_author.author_id', '=', 'authors.id')
            ->leftJoin('categories', 'posts.category_id', '=', 'categories.id')
            ->where('authors.slug', $this->slug)
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
            'name' => $this->name,
            'slug' => $this->slug,
            'postCount' => $this->postCount,
        ];
    }

    public static function fromLivewire($value): Author
    {
        return new self(
            $value->name,
            $value->slug,
            $value->postCount
        );
    }
}
