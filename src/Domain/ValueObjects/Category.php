<?php

namespace CupOfThea\MarkdownBlog\Domain\ValueObjects;

use Livewire\Wireable;

class Category implements Wireable
{
    private function __construct(
        public string $title,
        public string $slug,
    )
    {
    }

    public static function from(string $title, string $slug): Category
    {
        return new self($title, $slug);
    }

    public function toLivewire(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
        ];
    }

    public static function fromLivewire($value): Category
    {
        return new self($value->title, $value->slug,);
    }
}
