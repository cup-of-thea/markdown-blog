<?php

namespace Thea\MarkdownBlog\Exceptions;

use Exception;

class MissingPostTitleException extends Exception
{
    public function __construct()
    {
        parent::__construct("The post title is missing.");
    }
}
