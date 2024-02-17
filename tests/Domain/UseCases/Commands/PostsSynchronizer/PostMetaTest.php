<?php

namespace Domain\UseCases\Commands\PostsSynchronizer;

use Carbon\Carbon;
use CupOfThea\MarkdownBlog\Domain\ValueObjects\PostMeta;
use CupOfThea\MarkdownBlog\Exceptions\MissingPostDateException;
use CupOfThea\MarkdownBlog\Exceptions\MissingPostTitleException;
use PHPUnit\Framework\TestCase;

class PostMetaTest extends TestCase
{

    public function test_it_parses_yaml_data_into_post_meta(): void
    {
        $postMeta = PostMeta::parse(<<<CONTENT
---
title: "My first post"
date: "2021-01-01"
category: "My category"
tags: ["tag1", "tag2"]
---

# My first post

bla bla bla
CONTENT
        );

        $this->assertEquals('My first post', $postMeta->title);
        $this->assertEquals(new Carbon('2021-01-01'), $postMeta->date);
        $this->assertEquals('My category', $postMeta->category);
        $this->assertEquals(['tag1', 'tag2'], $postMeta->tags);
    }

    public function test_it_fails_when_title_is_missing(): void
    {
        $this->expectException(MissingPostTitleException::class);
        $this->expectExceptionMessage('The post title is missing.');

        PostMeta::parse(<<<CONTENT
---
date: "2021-01-01"
category: "My category"
tags: ["tag1", "tag2"]
---

# My first post

bla bla bla
CONTENT
        );
    }

    public function test_it_fails_when_date_is_missing(): void
    {
        $this->expectException(MissingPostDateException::class);
        $this->expectExceptionMessage('The post date is missing.');

        PostMeta::parse(<<<CONTENT
---
title: "My first post"
category: "My category"
tags: ["tag1", "tag2"]
---

# My first post

bla bla bla
CONTENT
        );
    }
}
