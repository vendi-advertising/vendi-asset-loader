<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader;

use Webmozart\PathUtil\Path;

class VendiAssetLoader
{
    public static function enqueue_simple()
    {
        self::enqueue_css_simple();
        self::enqueue_js_simple();
    }

    public static function enqueue_js_simple()
    {
        $default_dir = Path::join(get_template_directory(), 'js');
        $default_url = Path::join(get_template_directory_uri(), 'js');

        $media_dir   = apply_filters('vendi/asset_loader/get_media_dir_abs', $default_dir );
        $media_url   = apply_filters('vendi/asset_loader/get_media_url_abs', $default_url );

        $js_search_pattern = apply_filters('vendi/asset_loader/get_js_pattern', '[0-9][0-9][0-9]-*.js');

        $pattern = Path::join($media_dir, $js_search_pattern);

        $js_files = glob($pattern);
        if(false === $js_files || !\count($js_files)){
            return;
        }

        self::_do_enqueue_js($js_files, $media_dir, $media_url, true);
    }

    public static function enqueue_js_high_low()
    {
        $default_dir = Path::join(get_template_directory(), 'js');
        $default_url = Path::join(get_template_directory_uri(), 'js');

        $media_dir   = apply_filters('vendi/asset_loader/get_media_dir_abs', $default_dir );
        $media_url   = apply_filters('vendi/asset_loader/get_media_url_abs', $default_url );
        $high_low    = apply_filters('vendi/asset_loader/get_js_high_low_folders', ['header' => false, 'footer' => true]);

        $js_search_pattern = apply_filters('vendi/asset_loader/get_js_pattern', '[0-9][0-9][0-9]-*.js');

        if(!\is_iterable($high_low)){
            throw new \Exception('Return value of vendi/asset_loader/get_css_media_types must be iterable');
        }

        $pattern = Path::join($media_dir, $js_search_pattern);

        foreach($high_low as $type => $in_footer){
            $pattern = Path::join($media_dir, $type, $js_search_pattern);

            $js_files = glob($pattern);
            if(false === $js_files || !\count($js_files)){
                continue;
            }

            self::_do_enqueue_js($js_files, Path::join($media_dir, $type), Path::join($media_url, $type), $in_footer);
        }
    }

    public static function enqueue_css_simple()
    {
        $default_dir = Path::join(get_template_directory(), 'css');
        $default_url = Path::join(get_template_directory_uri(), 'css');

        $media_dir   = apply_filters('vendi/asset_loader/get_media_dir_abs', $default_dir );
        $media_url   = apply_filters('vendi/asset_loader/get_media_url_abs', $default_url );

        $css_search_pattern = apply_filters('vendi/asset_loader/get_css_pattern', '[0-9][0-9][0-9]-*.css');

        $pattern = Path::join($media_dir, $css_search_pattern);

        $css_files = glob($pattern);
        if(false === $css_files || !\count($css_files)){
            return;
        }

        self::_do_enqueue_css($css_files, $media_dir, $media_url, 'screen');
    }

    public static function enqueue_css_typed()
    {
        $default_dir = Path::join(get_template_directory(), 'css');
        $default_url = Path::join(get_template_directory_uri(), 'css');

        $media_dir   = apply_filters('vendi/asset_loader/get_media_dir_abs', $default_dir );
        $media_url   = apply_filters('vendi/asset_loader/get_media_url_abs', $default_url );
        $media_types = apply_filters('vendi/asset_loader/get_css_media_types', ['screen', 'all', 'print']);

        $css_search_pattern = apply_filters('vendi/asset_loader/get_css_pattern', '[0-9][0-9][0-9]-*.css');

        if(!\is_iterable($media_types)){
            throw new \Exception('Return value of vendi/asset_loader/get_css_media_types must be iterable');
        }

        foreach($media_types as $type){
            $pattern = Path::join($media_dir, $type, $css_search_pattern);

            $css_files = glob($pattern);
            if(false === $css_files || !\count($css_files)){
                continue;
            }

            self::_do_enqueue_css($css_files, Path::join($media_dir, $type), Path::join($media_url, $type), $type);
        }
    }

    public static function _do_enqueue_css(iterable $css_files, string $media_dir, string $media_url, string $type)
    {
        //Load each CSS file that starts with three digits followed by a dash in numerical order
        foreach( $css_files as $t ){

            $basename_with_extension    = \basename( $t );
            $basename_without_extension = \basename( $t, '.css' );

            \wp_enqueue_style(
                                "{$basename_without_extension}-style",
                                "{$media_url}/{$basename_with_extension}",
                                null,
                                \filemtime( "{$media_dir}/{$basename_with_extension}" ),
                                $type
                            );
        }
    }

    public static function _do_enqueue_js(iterable $js_files, string $media_dir, string $media_url, bool $in_footer)
    {
        //Load each CSS file that starts with three digits followed by a dash in numerical order
        foreach( $js_files as $t ){

            $basename_with_extension    = \basename( $t );
            $basename_without_extension = \basename( $t, '.js' );

            \wp_enqueue_script(
                                "{$basename_without_extension}-script",
                                "{$media_url}/{$basename_with_extension}",
                                null,
                                \filemtime( "{$media_dir}/{$basename_with_extension}" ),
                                $in_footer
                            );
        }
    }
}
