<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader;

interface LoaderInterface
{
    public function enqueue_files() : int;

    public function get_files(string $file_type, string $extra_folder = null) : ?iterable;
}
