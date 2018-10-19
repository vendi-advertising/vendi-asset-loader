<?php

declare(strict_types=1);

if (! function_exists('apply_filters')) {
    function apply_filters($tag, $value)
    {
        global $apply_filters_function;
        if (!is_callable($apply_filters_function)) {
            return $value;
        }

        $args = func_get_args();
        array_shift($args);
        return $apply_filters_function(...$args);
    }
}

if (! function_exists('get_template_directory')) {
    function get_template_directory()
    {
        global $current_test_dir;
        return $current_test_dir;
    }
}

if (! function_exists('get_template_directory_uri')) {
    function get_template_directory_uri()
    {
        global $current_test_url;
        return $current_test_url;
    }
}

if (!function_exists('wp_enqueue_script')) {
    function wp_enqueue_script($handle, $url, $deps, $version, $in_footer)
    {
        global $vendi_asset_scripts;

        if (!is_array($vendi_asset_scripts)) {
            $vendi_asset_scripts = [];
        }

        $vendi_asset_scripts[$handle] = [$handle, $url, $deps, $version, $in_footer];
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $url, $deps, $version, $media)
    {
        global $vendi_asset_styles;

        if (!is_array($vendi_asset_styles)) {
            $vendi_asset_styles = [];
        }

        $vendi_asset_styles[$handle] = [$handle, $url, $deps, $version, $media];
    }
}
