<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Js;

use Vendi\VendiAssetLoader\CommonLoaderBase;

abstract class JsLoaderBase extends CommonLoaderBase
{
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
        $count = 0;

        //Load each CSS file that starts with three digits followed by a dash in numerical order
        foreach ($files as $t) {
            $count++;

            $basename_with_extension    = \basename($t);
            $basename_without_extension = \basename($t, '.js');

            \wp_enqueue_script(
                                //Handle
                                "{$basename_without_extension}-script",

                                //URL
                                "{$media_url}/{$basename_with_extension}",

                                //Dependencies
                                null,

                                //Version cache buster
                                \filemtime("{$media_dir}/{$basename_with_extension}"),

                                //Whether to load in the footer (true) or header (false)
                                $in_footer
                            );
        }

        return $count;
    }
}
