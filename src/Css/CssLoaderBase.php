<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Css;

use Vendi\VendiAssetLoader\CommonLoaderBase;
use Webmozart\PathUtil\Path;

abstract class CssLoaderBase extends CommonLoaderBase
{
    abstract public function enqueue_files();

    final public function enqueue_files_with_optional_type(string $type = null)
    {
        $files = $this->get_files('css', $type);

        if(!$files){
            return;
        }

        //Call the actual worker. If we weren't given a type assume it is just screen
        //TODO: Maybe change screen to all? Vendi has used screen so we might
        //want to keep that for backwards compatibility.
        $this->actually_enqueue_files($files, $media_dir, $media_url, $type ? $type : 'screen' );
    }

    final public function actually_enqueue_files(iterable $files, string $media_dir, string $media_url, string $type)
    {
        foreach( $files as $t ){

            $basename_with_extension    = \basename( $t );
            $basename_without_extension = \basename( $t, '.css' );

            \wp_enqueue_style(
                                //Handle
                                "{$basename_without_extension}-style",

                                //URL
                                "{$media_url}/{$basename_with_extension}",

                                //Dependencies
                                null,

                                //Version cache buster
                                \filemtime( "{$media_dir}/{$basename_with_extension}" ),

                                //Media type
                                $type
                            );
        }
    }
}
