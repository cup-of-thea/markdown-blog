<?php

namespace CupOfThea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\Pipes;

use ParsedownExtra;

class ToHtml
{
    public function __invoke(string $content, \Closure $next)
    {
        $content = (new ParsedownExtra())
            ->setBreaksEnabled(false)
            ->setMarkupEscaped(false)
            ->text($content);

        return $next($content);
    }
}
