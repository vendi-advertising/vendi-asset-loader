<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader;

use Vendi\VendiAssetLoader\Css\SimpleCssLoader;
use Vendi\VendiAssetLoader\Css\TypedCssLoader;
use Vendi\VendiAssetLoader\Js\SimpleJsLoader;
use Vendi\VendiAssetLoader\Js\SortedJsLoader;

function load_simple_assets(): void
{
    $loaders = [
                    new SimpleCssLoader(),
                    new SimpleJsLoader(),
    ];

    foreach ($loaders as $loader) {
        $loader->enqueue_files();
    }
}

function load_typed_css(): void
{
    (new TypedCssLoader())->enqueue_files();
}

function load_sorted_js(): void
{
    (new SortedJsLoader())->enqueue_files();
}
