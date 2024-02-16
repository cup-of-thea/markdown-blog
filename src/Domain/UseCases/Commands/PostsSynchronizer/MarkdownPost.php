<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Pipeline;

readonly class MarkdownPost
{
    public static function parse(string $content, string $filePath, mixed $meta): self
    {
        $content = Pipeline::send($content)
            ->through([
                RemoveMeta::class,
                ToHtml::class
            ])
            ->thenReturn();

        $slug = $meta['slug'] ?? str($filePath)
            ->afterLast('/')
            ->trim()
            ->replace(' ', '-')
            ->before('.');

        return new self(
            title: $meta['title'],
            slug: $slug,
            content: $content,
            filePath: $filePath,
            date: new Carbon($meta['date'])
        );
    }

    private function __construct(
        public string $title,
        public string $slug,
        public string $content,
        public string $filePath,
        public Carbon $date,
    ){}

    public function toPostAttributes(): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'filePath' => $this->filePath,
            'date' => $this->date->locale('fr'),
        ];
    }
}
