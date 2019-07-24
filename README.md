# vendi-asset-loader

[![Build Status](https://travis-ci.org/vendi-advertising/vendi-asset-loader.svg?branch=master)](https://travis-ci.org/vendi-advertising/vendi-asset-loader)
[![codecov](https://codecov.io/gh/vendi-advertising/vendi-asset-loader/branch/master/graph/badge.svg)](https://codecov.io/gh/vendi-advertising/vendi-asset-loader)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Maintainability](https://api.codeclimate.com/v1/badges/72f384ac770998fa5147/maintainability)](https://codeclimate.com/github/vendi-advertising/vendi-asset-loader/maintainability)

Version 1 was a more complex version that supported multiple loading paths. It has since been rewritten to be much simpler and follow common patterns with less customizations (that were almost never used). Please only use version 2 going forward. You can find the [version 1 documention here](./docs/v1.md).

## Install
```composer require vendi-advertising/vendi-asset-loader:^2```

## Configure
The asset loader relies almost completely on the following environment variables.

*NOTE!* This module does not actually perform logic related to `.env` files, it is up to the theme to handle this if needed. Generally speaking, however, you can usually create a file named `.env.local` with these values to override. Otherwise the system assumes true environment variables.

  * `THEME_MODE`
    * Supported values:
      * `prod` - Forces both `THEME_CSS_MODE` and `THEME_JS_MODE` into `static` mode
      * `stage` - No difference currently
      * `dev` (_default_) - No difference currently
  * `THEME_CSS_MODE`
    * Supported values:
      * `dynamic` (_default_) - Loads CSS files from the `/css/` relative to the path theme root following the glob pattern `/css/[0-9][0-9][0-9]-*.css`
      * `static` - Loads CSS files as defined in the webpack encore entry file
    * Usage notes:
      * If `THEME_MODE` is set to `prod`, this configured value is ignore and instead `static` is always used
  * `THEME_JS_MODE`
    * Supported values:
      * `dynamic` (_default_) - Loads JS files from the `/js/` relative to the path theme root following the glob pattern `/js/[0-9][0-9][0-9]-*.js`
      * `static` - Loads JS files as defined in the webpack encore entry file
    * Usage notes:
      * If `THEME_MODE` is set to `prod`, this configured value is ignore and instead `static` is always used
  * `THEME_WEBPACK_ENTRY_FILE`
    * Supported values:
      * Either the absolute path to the webpack file or a path relative to the theme's root
      * Default: `./static/build/entrypoints.json`
  * `THEME_WEBPACK_ENTRY_DEFAULT`
    * Supported values:
      * The name for the theme's default entry that should represent the bulk of the CSS and JS required to load the site.
      * Default: `main`

## Usage
```
add_action(
            'wp_enqueue_scripts',
            function()
            {
                \Vendi\VendiAssetLoader\Loader::enqueue_default();
            }
        );

```
