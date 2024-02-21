<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\Pipes;

class RemoveMeta
{
    public function __invoke(string $content, \Closure $next)
    {
        $content = str($content)->after('---')->after('---')->trim()->toString();

        return $next($content);
    }
}
