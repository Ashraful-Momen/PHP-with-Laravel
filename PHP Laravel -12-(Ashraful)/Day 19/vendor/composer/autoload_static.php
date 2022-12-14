<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc47d06602f3b0b397d601be105aa1e00
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
            $loader->prefixLengthsPsr4 = ComposerStaticInitc47d06602f3b0b397d601be105aa1e00::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc47d06602f3b0b397d601be105aa1e00::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc47d06602f3b0b397d601be105aa1e00::$classMap;

        }, null, ClassLoader::class);
    }
}
