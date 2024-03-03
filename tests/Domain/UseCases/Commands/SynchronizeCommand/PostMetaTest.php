<?php

namespace Domain\UseCases\Commands\SynchronizeCommand;

use Carbon\Carbon;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Thea\MarkdownBlog\Domain\ValueObjects\PostMeta;
use Thea\MarkdownBlog\Exceptions\MissingPostDateException;
use Thea\MarkdownBlog\Exceptions\MissingPostTitleException;

class PostMetaTest extends TestCase
{
    /**
     * @throws MissingPostTitleException
     * @throws MissingPostDateException
     */
    public function test_it_parses_yaml_data_into_post_meta(): void
    {
        $postMeta = PostMeta::parse(<<<CONTENT
---
title: "My first post"
description: "My first post description"
date: "2021-01-01"
category: "My category"
tags: ["tag1", "tag2"]
canonical: "https://example.com/my-first-post"
authors: ["Thea", "Jane"]
---

# My first post

bla bla bla
CONTENT
        );

        $this->assertEquals('My first post', $postMeta->title);
        $this->assertEquals(new Carbon('2021-01-01'), $postMeta->date);
        $this->assertEquals('My category', $postMeta->category);
        $this->assertEquals(['tag1', 'tag2'], $postMeta->tags);
        $this->assertEquals('https://example.com/my-first-post', $postMeta->canonical);
        $this->assertEquals(['Thea', 'Jane'], $postMeta->authors);
        $this->assertEquals('My first post description', $postMeta->description);
    }

    /**
     * @throws MissingPostDateException
     */
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

    /**
     * @throws MissingPostTitleException
     */
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

    public function test_it_fails_when_description_is_too_long(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The description length must be less than 255 characters.');

        $description = str_repeat('a', 256);
        PostMeta::parse(<<<CONTENT
---
title: "My first post"
category: "My category"
date: 2021-01-01
description: {$description}
tags: ["tag1", "tag2"]
---

# My first post

bla bla bla
CONTENT
        );
    }

}
