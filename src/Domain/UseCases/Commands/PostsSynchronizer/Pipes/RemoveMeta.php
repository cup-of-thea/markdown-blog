<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\Pipes;

class RemoveMeta
{
    public function __invoke(string $content, \Closure $next)
    {
        $content = str($content)->afterLast('---')->trim()->toString();

        return $next($content);
    }
}
