<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader;

use Webmozart\Glob\Glob;

use function get_template_directory;
use function get_template_directory_uri;
use function untrailingslashit;

final class Loader
{
    private ?string $_media_dir = null;
    private ?string $_media_url = null;

    public function get_media_dir(): string
    {
        if (!$this->_media_dir) {
            $this->_media_dir = untrailingslashit(get_template_directory());
        }

        return $this->_media_dir;
    }

    public function get_media_url(): string
    {
        if (!$this->_media_url) {
            $this->_media_url = untrailingslashit(get_template_directory_uri());
        }

        return $this->_media_url;
    }

    public function enqueue_css_dynamic(): void
    {
        $media_dir = $this->get_media_dir();
        $media_url = $this->get_media_url();
        $css_files = Glob::glob($media_dir . '/css/[0-9][0-9][0-9]-*.css');

        if (count($css_files) > 0) {
            //Load each CSS file that starts with three digits followed by a dash in numerical order
            foreach ($css_files as $t) {
                wp_enqueue_style(
                    basename($t, '.css') . '-p-style',
                    $media_url . '/css/' . basename($t),
                    null,
                    filemtime($media_dir . '/css/' . basename($t)),
                    'screen'
                );
            }
        }
    }

    public function enqueue_css(): void
    {
        $this->enqueue_css_dynamic();
    }

    public function enqueue_js_dynamic(): void
    {
        $media_dir = $this->get_media_dir();
        $media_url = $this->get_media_url();
        $js_files = Glob::glob($media_dir . '/js/[0-9][0-9][0-9]-*.js');

        if (count($js_files) > 0) {
            //Load each JS file that starts with three digits followed by a dash in numerical order
            foreach ($js_files as $t) {
                wp_enqueue_script(
                    basename($t, '.js') . '-p-style',
                    $media_url . '/js/' . basename($t),
                    null,
                    filemtime($media_dir . '/js/' . basename($t)),
                    true
                );
            }
        }
    }

    public function enqueue_js(): void
    {
        $this->enqueue_js_dynamic();
    }

    public static function enqueue_default(): void
    {
        $obj = new self();
        $obj->enqueue_css();
        $obj->enqueue_js();
    }
}
