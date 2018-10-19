<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader;

use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

abstract class CommonLoaderBase implements LoaderInterface
{
    public const CSS_LOADER = 'css';
    public const JS_LOADER = 'js';

    abstract public function enqueue_files() : int;

    private $_type;

    public function __construct(string $type)
    {
        if (!in_array($type, [self::CSS_LOADER, self::JS_LOADER])) {
            throw new \Exception('Unknown asset type: ' . $type);
        }

        $this->_type = $type;
    }

    final public function _get_dir_and_url_tuple(string $file_type, string $extra_folder = null) : array
    {
        //By default we'll assume that we're relative to the theme's root
        $default_dir = Path::join(\get_template_directory(), $file_type);
        $default_url = Path::join(\get_template_directory_uri(), $file_type);

        //Allow for overrides
        $media_dir   = \apply_filters('vendi/asset_loader/get_media_dir_abs', $default_dir);
        $media_url   = \apply_filters('vendi/asset_loader/get_media_url_abs', $default_url);

        //If we've been given an extra folder, append that to the folder paths
        if ($extra_folder) {
            $media_dir = Path::join($media_dir, $extra_folder);
            $media_url = Path::join($media_url, $extra_folder);
        }

        return [
                    'media_dir' => $media_dir,
                    'media_url' => $media_url,
        ];
    }

    final public function get_files(string $file_type, string $extra_folder = null) : ?iterable
    {
        if (!\in_array($file_type, ['css', 'js'])) {
            throw new \Exception('Method _get_files only support CSS and JS');
        }

        extract($this->_get_dir_and_url_tuple($file_type, $extra_folder));

        //Sanity check that we actually have a directory. The typed loader tests
        //for common media types however we don't require each type to have
        //a directory.
        if (!\is_dir($media_dir)) {
            return null;
        }

        //Assume three digit syntax
        $file_name_search_pattern = \apply_filters("vendi/asset_loader/get_{$file_type}_pattern", "[0-9][0-9][0-9]-*.{$file_type}");

        //Since we are globbing, we need an absolute path, so join here
        $abs_search_pattern = Path::join($media_dir, $file_name_search_pattern);

        //Search for files. The vfsStream doesn't support glob() so we are using
        //a wrapper. Not ideal, but they fallback to real glob() whenever possible.
        //TODO: Maybe switch to either DirectoryIterator or opendir()
        $files = Glob::glob($abs_search_pattern);

        //Glob can return false. I don't think it can return an empty array
        //but you know... just in case.
        if (false === $files || !\count($files)) {
            return null;
        }

        return $files;
    }

    final public function _actually_enqueue_files_impl(callable $func, iterable $files, string $media_dir, string $media_url, $last) : int
    {
        $count = 0;

        $ext = null;
        $type = null;

        switch ($this->_type) {
            case 'js':
                $ext = $this->_type;
                $type = 'script';
                break;

            case 'css':
                $ext = $this->_type;
                $type = 'style';
                break;
        }

        foreach ($files as $t) {
            $count++;

            $basename_with_extension    = \basename($t);
            $basename_without_extension = \basename($t, ".{$ext}");

            $func(
                    //Handle
                    "{$basename_without_extension}-{$type}",

                    //URL
                    "{$media_url}/{$basename_with_extension}",

                    //Dependencies
                    null,

                    //Version cache buster
                    \filemtime("{$media_dir}/{$basename_with_extension}"),

                    //Whether to load in the footer (true) or header (false)
                    //for JS or media type for CSS
                    $last
                );
        }

        return $count;
    }
}
