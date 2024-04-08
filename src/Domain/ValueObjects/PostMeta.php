<?php

namespace Thea\MarkdownBlog\Domain\ValueObjects;

use Carbon\Carbon;
use InvalidArgumentException;
use Symfony\Component\Yaml\Yaml;
use Thea\MarkdownBlog\Exceptions\MissingPostDateException;
use Thea\MarkdownBlog\Exceptions\MissingPostTitleException;

readonly class PostMeta
{
    /**
     * @throws MissingPostTitleException
     * @throws MissingPostDateException
     */
    public static function parse(string $content): self
    {
        $params = Yaml::parse(str($content)->after('---')->before('---')->trim()->toString());

        self::ensureDescriptionLengthIsValid($params);

        return new self(
            title: $params['title'] ?? throw new MissingPostTitleException(),
            slug: str($params['title'])->slug(),
            date: isset($params['date']) ? new Carbon($params['date']) : throw new MissingPostDateException(),
            category: $params['category'] ?? null,
            edition: $params['edition'] ?? null,
            description: $params['description'] ?? null,
            tags: $params['tags'] ?? null,
            canonical: $params['canonical'] ?? null,
            authors: $params['authors'] ?? null,
            series: $params['series'] ?? null,
            episode: $params['episode'] ?? null,
        );
    }

    private function __construct(
        public string  $title,
        public string  $slug,
        public Carbon  $date,
        public ?string $category,
        public ?string $edition,
        public ?string $description,
        public ?array  $tags,
        public ?string $canonical,
        public ?array  $authors,
        public ?string $series,
        public ?string $episode,
    ) {}

    private static function ensureDescriptionLengthIsValid(mixed $params): void
    {
        if (isset($params['description']) && strlen($params['description']) > 255) {
            throw new InvalidArgumentException('The description length must be less than 255 characters.');
        }
    }
}
