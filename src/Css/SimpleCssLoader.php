<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Css;

final class SimpleCssLoader extends CssLoaderBase
{
    public function enqueue_files()
    {
        //Don't pass anything so that we get the standard single-folder behavior
        $this->enqueue_files_with_optional_type();
    }
}
