# vendi-asset-loader

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
