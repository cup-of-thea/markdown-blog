<?php

namespace Thea\MarkdownBlog\Exceptions;

use Exception;

class MissingPostDateException extends Exception
{
    public function __construct()
    {
        parent::__construct("The post date is missing.");
    }
}
