<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Css;

final class TypedCssLoader extends CssLoaderBase
{
    public function enqueue_files()
    {
        //These are our supported types by default
        $media_types = apply_filters('vendi/asset_loader/get_css_media_types', ['screen', 'all', 'print']);

        //The typed loader is an advanced loader so we can throw for exceptional cases
        if (!\is_iterable($media_types)) {
            throw new \Exception('Return value of vendi/asset_loader/get_css_media_types must be iterable');
        }

        foreach ($media_types as $type) {
            $this->enqueue_files_with_optional_type($type);
        }
    }
}
