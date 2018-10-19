<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Css;

use Vendi\VendiAssetLoader\CommonLoaderBase;

abstract class CssLoaderBase extends CommonLoaderBase
{
    public function __construct()
    {
        parent::__construct(self::CSS_LOADER);
    }

    final public function enqueue_files_with_optional_type(string $type = null) : int
    {
        $files = $this->get_files('css', $type);

        if (!$files) {
            return 0;
        }

        extract($this->_get_dir_and_url_tuple('css', $type));

        //Call the actual worker. If we weren't given a type assume it is just screen
        //TODO: Maybe change screen to all? Vendi has used screen so we might
        //want to keep that for backwards compatibility.
        return $this->actually_enqueue_files($files, $media_dir, $media_url, $type ? $type : 'screen');
    }

    final public function actually_enqueue_files(iterable $files, string $media_dir, string $media_url, string $type) : int
    {
        return $this->_actually_enqueue_files_impl('wp_enqueue_style', $files, $media_dir, $media_url, $type);
    }
}
