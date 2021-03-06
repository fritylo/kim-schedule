<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite53ba8c1faa935da2b53057bb96fef61
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        'a4a119a56e50fbb293281d9a48007e0e' => __DIR__ . '/..' . '/symfony/polyfill-php80/bootstrap.php',
        '667aeda72477189d0494fecd327c3641' => __DIR__ . '/..' . '/symfony/var-dumper/Resources/functions/dump.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Php80\\' => 23,
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\VarDumper\\' => 28,
        ),
        'P' => 
        array (
            'Phug\\' => 5,
        ),
        'N' => 
        array (
            'NodejsPhpFallback\\' => 18,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Php80\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-php80',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\VarDumper\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/var-dumper',
        ),
        'Phug\\' => 
        array (
            0 => __DIR__ . '/..' . '/phug/phug/src/Phug/Ast',
            1 => __DIR__ . '/..' . '/phug/phug/src/Phug/Compiler',
            2 => __DIR__ . '/..' . '/phug/phug/src/Phug/DependencyInjection',
            3 => __DIR__ . '/..' . '/phug/phug/src/Phug/Event',
            4 => __DIR__ . '/..' . '/phug/phug/src/Phug/Formatter',
            5 => __DIR__ . '/..' . '/phug/phug/src/Phug/Invoker',
            6 => __DIR__ . '/..' . '/phug/phug/src/Phug/Lexer',
            7 => __DIR__ . '/..' . '/phug/phug/src/Phug/Parser',
            8 => __DIR__ . '/..' . '/phug/phug/src/Phug/Reader',
            9 => __DIR__ . '/..' . '/phug/phug/src/Phug/Renderer',
            10 => __DIR__ . '/..' . '/phug/phug/src/Phug/Util',
        ),
        'NodejsPhpFallback\\' => 
        array (
            0 => __DIR__ . '/..' . '/nodejs-php-fallback/nodejs-php-fallback/src/NodejsPhpFallback',
        ),
    );

    public static $fallbackDirsPsr4 = array (
        0 => __DIR__ . '/..' . '/js-transformer/js-transformer/src',
        1 => __DIR__ . '/..' . '/pug-php/pug/src',
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'Pug\\' => 
            array (
                0 => __DIR__ . '/..' . '/pug-php/pug/src',
            ),
            'Phug\\' => 
            array (
                0 => __DIR__ . '/..' . '/phug/js-transformer-filter/src',
            ),
        ),
        'J' => 
        array (
            'JsPhpize' => 
            array (
                0 => __DIR__ . '/..' . '/js-phpize/js-phpize/src',
                1 => __DIR__ . '/..' . '/js-phpize/js-phpize-phug/src',
            ),
            'Jade\\' => 
            array (
                0 => __DIR__ . '/..' . '/pug-php/pug/src',
            ),
        ),
    );

    public static $fallbackDirsPsr0 = array (
        0 => __DIR__ . '/..' . '/phug/phug/src/Phug/Phug',
    );

    public static $classMap = array (
        'Attribute' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Attribute.php',
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Stringable' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/Stringable.php',
        'UnhandledMatchError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/UnhandledMatchError.php',
        'ValueError' => __DIR__ . '/..' . '/symfony/polyfill-php80/Resources/stubs/ValueError.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite53ba8c1faa935da2b53057bb96fef61::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite53ba8c1faa935da2b53057bb96fef61::$prefixDirsPsr4;
            $loader->fallbackDirsPsr4 = ComposerStaticInite53ba8c1faa935da2b53057bb96fef61::$fallbackDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInite53ba8c1faa935da2b53057bb96fef61::$prefixesPsr0;
            $loader->fallbackDirsPsr0 = ComposerStaticInite53ba8c1faa935da2b53057bb96fef61::$fallbackDirsPsr0;
            $loader->classMap = ComposerStaticInite53ba8c1faa935da2b53057bb96fef61::$classMap;

        }, null, ClassLoader::class);
    }
}
