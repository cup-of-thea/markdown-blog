<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbfaf3940753d3fb2ba5af81200a20bdf
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'CupOfThea\\MarkdownBlog\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'CupOfThea\\MarkdownBlog\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbfaf3940753d3fb2ba5af81200a20bdf::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbfaf3940753d3fb2ba5af81200a20bdf::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbfaf3940753d3fb2ba5af81200a20bdf::$classMap;

        }, null, ClassLoader::class);
    }
}