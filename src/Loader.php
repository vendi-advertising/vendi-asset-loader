<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

final class Loader
{
    const THEME_MODE_DEV = 'dev';
    const THEME_MODE_PROD = 'prod';
    const THEME_MODE_PRODUCTION_LEGACY = 'production';
    const THEME_MODE_STAGE = 'stage';

    const THEME_MODE_ASSET_DYNAMIC = 'dynamic';
    const THEME_MODE_ASSET_STATIC = 'static';

    const DEFAULT_WEBPACK_ENTRY_NAME = 'main';

    private $_media_dir;
    private $_media_url;

    public function get_env(string $name) : string
    {
        $ret = \getenv($name);
        if(false === $ret){
            return '';
        }
        return $ret;
    }

    public function get_theme_mode() : string
    {
        switch(\mb_strtolower($this->get_env('THEME_MODE'))){
            case self::THEME_MODE_PROD:
            case self::THEME_MODE_PRODUCTION_LEGACY:
                return self::THEME_MODE_PROD;

            case self::THEME_MODE_STAGE:
                return self::THEME_MODE_STAGE;

            default:
                return self::THEME_MODE_DEV;
        }
    }

    public function get_theme_css_mode() : string
    {
        //In the production env, assets must always use the builder
        if(self::THEME_MODE_PROD === $this->get_theme_mode()) {
            return self::THEME_MODE_ASSET_STATIC;
        }

        switch(\mb_strtolower($this->get_env('THEME_CSS_MODE'))){
            case self::THEME_MODE_ASSET_STATIC:
                return self::THEME_MODE_ASSET_STATIC;

            default:
                return self:: THEME_MODE_ASSET_DYNAMIC;
        }
    }

    public function get_theme_js_mode() : string
    {
        //In the production env, assets must always use the builder
        if(self::THEME_MODE_PROD === $this->get_theme_mode()) {
            return self::THEME_MODE_ASSET_STATIC;
        }

        switch(\mb_strtolower($this->get_env('THEME_JS_MODE'))){
            case self::THEME_MODE_ASSET_STATIC:
                return self::THEME_MODE_ASSET_STATIC;

            default:
                return self:: THEME_MODE_ASSET_DYNAMIC;
        }
    }

    public function get_media_dir() : string
    {
        if(!$this->_media_dir) {
            $this->_media_dir = \untrailingslashit( \get_template_directory() );
        }

        return $this->_media_dir;
    }

    public function get_media_url() : string
    {
        if(!$this->_media_url) {
            $this->_media_url = \untrailingslashit( \get_template_directory_uri() );
        }

        return $this->_media_url;
    }

    public function get_webpack_entry_file() : string
    {
        $entry = $this->get_env('THEME_WEBPACK_ENTRY_FILE');
        if($entry){

            //I don't like this but Path::isAbsolute doesn't support stream wrappers
            if(\is_file($entry)){
                return $entry;
            }

            //makeAbsolute doesn't work against streams, apparently
            return Path::makeAbsolute($entry, $this->get_media_dir());
        }

        //This is the default
        return Path::join($this->get_media_dir() . '/static/build/entrypoints.json');
    }

    public function get_webpack_default_entry_name() : string
    {
        $name = $this->get_env('THEME_WEBPACK_ENTRY_DEFAULT');
        if($name){
            return $name;
        }

        return self::DEFAULT_WEBPACK_ENTRY_NAME;
    }

    public function get_entries_by_name(string $entry_name) : array
    {
        $file_to_load = $this->get_webpack_entry_file();
        if(!is_file($file_to_load)){
            throw new \Exception('Please run "yarn install && yarn encore production" before continuing');
        }

        try{
            $entrypoints = json_decode(file_get_contents($file_to_load), true, 512, JSON_THROW_ON_ERROR);
        }catch(\Exception $ex) {
            throw new \Exception('The main webpack entry file could not be loaded. Please rebuild it.');
        }


        foreach($entrypoints['entrypoints'] as $name => $app){
            if($name === $entry_name){
                return $app;
            }
        }

        return [];
    }

    public function enqueue_css(string $entry_name = null)
    {
        if(self::THEME_MODE_ASSET_DYNAMIC === $THEME_CSS_MODE){
            $media_dir = $this->get_media_dir();
            $media_url = $this->get_media_url();
            $css_files = Glob::glob( $media_dir . '/css/[0-9][0-9][0-9]-*.css' );

            if(false !== $css_files && count($css_files) > 0) {
                //Load each CSS file that starts with three digits followed by a dash in numerical order
                foreach( $css_files as $t ) {
                    wp_enqueue_style(
                                        basename( $t, '.css' ) . '-p-style',
                                        $media_url . '/css/' . basename( $t ),
                                        null,
                                        filemtime( $media_dir . '/css/' . basename( $t ) ),
                                        'screen'
                                    );
                }
            }

            return;
        }

        if(!$entry_name){
            $entry_name = $this->get_webpack_default_entry_name();
        }

        foreach($entrypoints['entrypoints'] as $name => $app){
            if($name === $entry_name){
                foreach($app as $type => $files){
                    foreach($files as $file){
                        switch ($type) {
                            case 'css':
                                wp_enqueue_style(
                                                    basename( $file, '.css' ) . '-style',
                                                    $file,
                                                    null,
                                                    null,
                                                    'screen'
                                                );
                                break;
                        }
                    }
                }
            }
        }
    }

    public function enqueue_js(string $entry_name = null)
    {
        if(self::THEME_MODE_ASSET_DYNAMIC === $THEME_CSS_MODE){
            $media_dir = $this->get_media_dir();
            $media_url = $this->get_media_url();
            $js_files = glob( $media_dir . '/js/[0-9][0-9][0-9]-*.js' );

            if(false !== $js_files && count($js_files) > 0) {
                //Load each JS file that starts with three digits followed by a dash in numerical order
                foreach( $js_files as $t ) {
                    wp_enqueue_script(
                                        basename( $t, '.js' ) . '-p-style',
                                        $media_url . '/js/' . basename( $t ),
                                        null,
                                        filemtime( $media_dir . '/js/' . basename( $t ) ),
                                        true
                                    );
                }
            }

            return;
        }

        if(!$entry_name){
            $entry_name = $this->get_webpack_default_entry_name();
        }

        foreach($entrypoints['entrypoints'] as $name => $app){
            if($name === $entry_name){
                foreach($app as $type => $files){
                    foreach($files as $file){
                        switch ($type) {
                            case 'js':
                                wp_enqueue_script(
                                                    basename( $file, '.js' ) . '-script',
                                                    $file,
                                                    null,
                                                    null,
                                                    true
                                                );
                                break;
                        }
                    }
                }
            }
        }
    }

    public static function enqueue_default()
    {
        $obj = new self();
        $obj->enqueue_css();
        $obj->enqueue_js();
    }
}
