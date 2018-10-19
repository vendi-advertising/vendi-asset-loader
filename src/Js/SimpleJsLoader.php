<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\Js;

final class SimpleJsLoader extends JsLoaderBase
{
    public function enqueue_files() : int
    {
        return $this->enqueue_files_with_optional_high_low(true);
    }
}
