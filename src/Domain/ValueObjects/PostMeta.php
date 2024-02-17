<?php

namespace CupOfThea\MarkdownBlog\Domain\ValueObjects;

use Carbon\Carbon;
use CupOfThea\MarkdownBlog\Exceptions\MissingPostDateException;
use CupOfThea\MarkdownBlog\Exceptions\MissingPostTitleException;
use Symfony\Component\Yaml\Yaml;

readonly class PostMeta
{
    /**
     * @throws MissingPostTitleException
     * @throws MissingPostDateException
     */
    public static function parse(string $content): self
    {
        $params = Yaml::parse(str($content)->after('---')->before('---')->trim()->toString());

        return new self(
            title: $params['title'] ?? throw new MissingPostTitleException(),
            slug: str($params['title'])->slug(),
            date: isset($params['date']) ? new Carbon($params['date']) : throw new MissingPostDateException(),
            category: $params['category'] ?? null,
            description: $params['description'] ?? null,
            tags: $params['tags'] ?? null
        );
    }

    private function __construct(
        public string  $title,
        public string  $slug,
        public Carbon  $date,
        public ?string $category,
        public ?string $description,
        public ?array  $tags
    ) {}
}
