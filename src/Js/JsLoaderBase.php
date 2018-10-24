<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Js;

use Vendi\VendiAssetLoader\CommonLoaderBase;

abstract class JsLoaderBase extends CommonLoaderBase
{
    final public function get_enqueue_function_for_specific_type() : callable
    {
        return 'wp_enqueue_script';
    }

    final public function get_extension_for_specific_type() : string
    {
        return 'js';
    }

    final public function get_handle_suffix_for_specific_type() : string
    {
        return 'script';
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
}
