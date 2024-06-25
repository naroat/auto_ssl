<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1c85d1463ac19e4994154e6b737abcdc
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit1c85d1463ac19e4994154e6b737abcdc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1c85d1463ac19e4994154e6b737abcdc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1c85d1463ac19e4994154e6b737abcdc::$classMap;

        }, null, ClassLoader::class);
    }
}