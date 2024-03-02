<?php

namespace Domain\UseCases\Commands\SynchronizeCommand\Pipes;

use Thea\MarkdownBlog\Domain\UseCases\Commands\PostsSynchronizer\Pipes\RemoveMeta;
use PHPUnit\Framework\TestCase;

class RemoveMetaTest extends TestCase
{
    public function test_it_removes_meta_from_content_and_keep_horizontal_rules(): void
    {
        $content = <<<CONTENT
---
title: "My first post"
date: "2021-01-01"
category: "My category"
tags: ["tag1", "tag2"]
---

# My first post

bla

---

bla bla bla
CONTENT;

        $removeMeta = new RemoveMeta();

        $content = $removeMeta($content, fn($content) => $content);

        $this->assertEquals(<<<CONTENT
# My first post

bla

---

bla bla bla
CONTENT, $content);
    }
}
