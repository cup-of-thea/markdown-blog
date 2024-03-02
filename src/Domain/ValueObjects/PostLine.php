<?php

namespace Thea\MarkdownBlog\Domain\ValueObjects;

use Carbon\Carbon;

readonly class PostLine
{
    public function __construct(
        public string $title,
        public string $slug,
        public Carbon $date,
        public ?string $category
    )
    {
    }

    public static function from($title, $slug, $date, $category): PostLine
    {
        return new self($title, $slug, Carbon::parse($date), $category);
    }
}
