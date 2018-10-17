<?php declare(strict_types=1);

if (! function_exists('apply_filters')) {
    function apply_filters($tag, $value)
    {
        return $value;
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
    }
}

if (!function_exists('wp_enqueue_style')) {
    function wp_enqueue_style($handle, $url, $deps, $version, $media)
    {
    }
}
