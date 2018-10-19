# vendi-asset-loader

[![Build Status](https://travis-ci.org/vendi-advertising/vendi-asset-loader.svg?branch=master)](https://travis-ci.org/vendi-advertising/vendi-asset-loader)
[![codecov](https://codecov.io/gh/vendi-advertising/vendi-asset-loader/branch/master/graph/badge.svg)](https://codecov.io/gh/vendi-advertising/vendi-asset-loader)


## Usage:

```
//Load CSS and JavaScript
add_action(
            'wp_enqueue_scripts',
            function()
            {
                // Vendi\VendiAssetLoader\VendiAssetLoader::enqueue_css_simple();
                // Vendi\VendiAssetLoader\VendiAssetLoader::enqueue_js_high_low();

                Vendi\VendiAssetLoader\VendiAssetLoader::enqueue_simple();
            }
        );
```
