<?php

namespace CupOfThea\MarkdownBlog\Domain\ValueObjects;

use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\Pipes\RemoveMeta;
use CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\Pipes\ToHtml;
use Illuminate\Support\Facades\Pipeline;

readonly class MarkdownPost
{
    public static function parse(string $content, string $filePath): self
    {
        $meta = PostMeta::parse($content);

        $content = Pipeline::send($content)
            ->through([
                RemoveMeta::class,
                ToHtml::class
            ])
            ->thenReturn();

        return new self(
            meta: $meta,
            content: $content,
            filePath: $filePath
        );
    }

    private function __construct(
        public PostMeta $meta,
        public string $content,
        public string $filePath,
    ){}

    public function toPostAttributes(): array
    {
        return [
            'title' => $this->meta->title,
            'slug' => $this->meta->slug,
            'content' => $this->content,
            'filePath' => $this->filePath,
            'date' => $this->meta->date->locale('fr'),
        ];
    }
}
