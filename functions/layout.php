<?php

/**
 * Add base.php File
 */

function brightbyte_template_path()
{
    return BrightByte_Wrapping::$main_template;
}

function brightbyte_template_base(): string
{
    return BrightByte_Wrapping::$base;
}

class BrightByte_Wrapping
{
    static $main_template;

    static string $base;

    static function wrap($template): string
    {
        self::$main_template = $template;
        self::$base = substr(basename(self::$main_template), 0, -4);
        if ('index' == self::$base)
            self::$base = false;
        $templates = array('base.php');
        if (self::$base)
            array_unshift($templates, sprintf('base-%s.php', self::$base));

        return locate_template($templates);
    }
}

add_filter('template_include', array('BrightByte_Wrapping', 'wrap'), 99);
