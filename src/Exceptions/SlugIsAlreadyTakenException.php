<?php

namespace CupOfThea\MarkdownBlog\Exceptions;

use Exception;

class SlugIsAlreadyTakenException extends Exception
{
    public function __construct(
        private string $slug,
        private string $savedFilePath,
        private string $newFilePath)
    {
        parent::__construct("The slug {$slug} is already taken : see {$savedFilePath} and {$newFilePath}");
    }

    public function context(): array
    {
        return [
            'slug' => $this->slug,
            'savedFilePath' => $this->savedFilePath,
            'newFilePath' => $this->newFilePath,
        ];
    }
}
