<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Js;

use Vendi\VendiAssetLoader\CommonLoaderBase;

abstract class JsLoaderBase extends CommonLoaderBase
{
    public function __construct()
    {
        parent::__construct(self::JS_LOADER);
    }

    final public function enqueue_files_with_optional_high_low(bool $in_footer, string $extra_folder = null) : int
    {
        $files = $this->get_files('js', $extra_folder);

        if (!$files) {
            return 0;
        }

        extract($this->_get_dir_and_url_tuple('js', $extra_folder));

        //Call the actual worker
        return $this->actually_enqueue_files($files, $media_dir, $media_url, $in_footer);
    }

    final public function actually_enqueue_files(iterable $files, string $media_dir, string $media_url, bool $in_footer) : int
    {
        return $this->_actually_enqueue_files_impl('wp_enqueue_script', $files, $media_dir, $media_url, $in_footer);
    }
}
