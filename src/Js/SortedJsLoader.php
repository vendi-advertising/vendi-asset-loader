<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Js;

use Webmozart\PathUtil\Path;

final class SortedJsLoader extends JsLoaderBase
{
    public function enqueue_files()
    {
        //Assume two folders, header and footer
        $high_low    = apply_filters('vendi/asset_loader/get_js_sorted_folders', ['header' => false, 'footer' => true]);

        //The sorted loader is an advanced loader so we can throw for exceptional cases
        if(!\is_iterable($high_low)){
            throw new \Exception('Return value of vendi/asset_loader/get_js_sorted_folders must be iterable');
        }

        foreach($high_low as $folder => $in_footer){
            $this->enqueue_files_with_optional_high_low($folder, $in_footer);
        }
    }
}
