<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit07c51b71c71cc4751d36bd5130be0938
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit07c51b71c71cc4751d36bd5130be0938::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit07c51b71c71cc4751d36bd5130be0938::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit07c51b71c71cc4751d36bd5130be0938::$classMap;

        }, null, ClassLoader::class);
    }
}
