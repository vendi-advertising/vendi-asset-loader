<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Css;

use Vendi\VendiAssetLoader\CommonLoaderBase;

abstract class CssLoaderBase extends CommonLoaderBase
{
    final public function get_enqueue_function_for_specific_type() : callable
    {
        return 'wp_enqueue_style';
    }

    final public function get_extension_for_specific_type() : string
    {
        return 'css';
    }

    final public function get_handle_suffix_for_specific_type() : string
    {
        return 'style';
    }

    final public function enqueue_files_with_optional_type(string $type = null) : int
    {
        $files = $this->get_files('css', $type);

        if (!$files) {
            return 0;
        }

        $result = $this->_get_dir_and_url_tuple('css', $type);
        extract($result);

        //Call the actual worker. If we weren't given a type assume it is just screen
        //TODO: Maybe change screen to all? Vendi has used screen so we might
        //want to keep that for backwards compatibility.
        return $this->actually_enqueue_files($files, $media_dir, $media_url, $type ?: 'screen');
    }
}
