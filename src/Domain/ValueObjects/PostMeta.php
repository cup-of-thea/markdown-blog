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
            reviewAuthors: $params['review_authors'] ?? null,
            image: $params['image'] ?? null,
            imageAlt: $params['image_alt'] ?? null,
            rows: $params['rows'] ?? 2,
            cols: $params['cols'] ?? 2,
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
        public ?string $reviewAuthors,
        public ?string $image,
        public ?string $imageAlt,
        public int    $rows,
        public int    $cols
    )
    {
    }

    private static function ensureDescriptionLengthIsValid(mixed $params): void
    {
        if (isset($params['description']) && strlen($params['description']) > 255) {
            throw new InvalidArgumentException('The description length must be less than 255 characters.');
        }
    }
}
