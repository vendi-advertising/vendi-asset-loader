# vendi-asset-loader

[![Build Status](https://travis-ci.org/vendi-advertising/vendi-asset-loader.svg?branch=master)](https://travis-ci.org/vendi-advertising/vendi-asset-loader)
[![codecov](https://codecov.io/gh/vendi-advertising/vendi-asset-loader/branch/master/graph/badge.svg)](https://codecov.io/gh/vendi-advertising/vendi-asset-loader)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Maintainability](https://api.codeclimate.com/v1/badges/72f384ac770998fa5147/maintainability)](https://codeclimate.com/github/vendi-advertising/vendi-asset-loader/maintainability)

## Usage:

### Standard/Simple
For a standard simple setup with `css` and `js` folders off of the theme's root, and with files prefixed with three digits you can use the below. It will enqueue all CSS and `screen` and put all JS in the footer which is usually good enough for most scenarios.
```
use function Vendi\VendiAssetLoader\load_simple_assets;

add_action(
            'wp_enqueue_scripts',
            function()
            {
                load_simple_assets();
            }
        );
```

## Typed CSS:
If you have sub folders in your CSS for `screen`, `all`, `print`, etc., you can use
```
use function Vendi\VendiAssetLoader\load_typed_css;

add_action(
            'wp_enqueue_scripts',
            function()
            {
                load_typed_css();
            }
        );
```

## Sort JS:
If you have sub folders in your JS for `header` and `footer` you can use:
```
use function Vendi\VendiAssetLoader\load_sorted_js;

add_action(
            'wp_enqueue_scripts',
            function()
            {
                Vendi\VendiAssetLoader\VendiAssetLoader::load_sorted_js();
            }
        );
```

## Hooks

### CSS file search pattern
 * Hook: `vendi/asset_loader/get_css_pattern`
 * Default: `[0-9][0-9][0-9]-*.css`
 
### JS file search pattern
 * Hook: `vendi/asset_loader/get_js_pattern`
 * Default: `[0-9][0-9][0-9]-*.js`

### Root dir for all relative paths
 * Hook: `vendi/asset_loader/get_media_dir_abs`
 * Default: `get_template_directory()`
 * Note: This directory is appended with the type for CSS and the header/footer folders for JS

### Root URL for all relative paths
 * Hook: `vendi/asset_loader/get_media_url_abs`
 * Default: `get_template_directory_uri()`
 * Note: This URL is appended with the type for CSS and the header/footer folders for JS

### Folders for header/footer JS
 * Hook: `vendi/asset_loader/get_js_sorted_folders`
 * Default: `['header' => false, 'footer' => true]`
 
### Folders for media types for CSS
 * Hook: `vendi/asset_loader/get_css_media_types`
 * Default: `['screen', 'all', 'print']`
