<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit91df34ae34a01d6a6ba2b81d7a1a50c8
{
    public static $prefixLengthsPsr4 = array (
        'e' => 
        array (
            'engine\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'engine\\' => 
        array (
            0 => __DIR__ . '/../..' . '/engine',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit91df34ae34a01d6a6ba2b81d7a1a50c8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit91df34ae34a01d6a6ba2b81d7a1a50c8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit91df34ae34a01d6a6ba2b81d7a1a50c8::$classMap;

        }, null, ClassLoader::class);
    }
}
