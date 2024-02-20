<?php

namespace CupOfThea\MarkdownBlog\Domain\ValueObjects;

use Carbon\Carbon;
use Livewire\Wireable;

class Post implements Wireable
{
    private function __construct(
        public string  $title,
        public string  $slug,
        public string  $content,
        public Carbon  $date,
    )
    {
    }

    public static function from(
        string  $title,
        string  $slug,
        string  $content,
        Carbon  $date
    ): Post
    {
        return new self($title, $slug, $content, $date);
    }

    public function toLivewire(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'date' => new Carbon($this->date),
        ];
    }

    public static function fromLivewire($value): Post
    {
        return new self(
            $value->title,
            $value->slug,
            $value->content,
            $value->date,
        );
    }
}
