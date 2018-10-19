<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Js;

final class SortedJsLoader extends JsLoaderBase
{
    public function enqueue_files() : int
    {
        //Assume two folders, header and footer
        $high_low    = apply_filters('vendi/asset_loader/get_js_sorted_folders', ['header' => false, 'footer' => true]);

        //The sorted loader is an advanced loader so we can throw for exceptional cases
        if (!\is_iterable($high_low)) {
            throw new \Exception('Return value of vendi/asset_loader/get_js_sorted_folders must be iterable');
        }

        $count = 0;
        foreach ($high_low as $folder => $in_footer) {
            $count += $this->enqueue_files_with_optional_high_low($folder, $in_footer);
        }
        return $count;
    }
}
