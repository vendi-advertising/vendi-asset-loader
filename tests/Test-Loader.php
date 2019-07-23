<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Loader;
use Webmozart\PathUtil\Path;

class Test_Loader extends test_base
{
    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_theme_mode
     */
    public function test___get_theme_mode()
    {
        //Default, not set
        $this->assertSame(Loader::THEME_MODE_DEV, (new Loader())->get_theme_mode());

        //Supported modes
        \putenv('THEME_MODE=dev');
        $this->assertSame(Loader::THEME_MODE_DEV, (new Loader())->get_theme_mode());

        \putenv('THEME_MODE=prod');
        $this->assertSame(Loader::THEME_MODE_PROD, (new Loader())->get_theme_mode());

        \putenv('THEME_MODE=production');
        $this->assertSame(Loader::THEME_MODE_PROD, (new Loader())->get_theme_mode());

        \putenv('THEME_MODE=stage');
        $this->assertSame(Loader::THEME_MODE_STAGE, (new Loader())->get_theme_mode());

        //Test upper case conversion
        \putenv('THEME_MODE=DEV');
        $this->assertSame(Loader::THEME_MODE_DEV, (new Loader())->get_theme_mode());

        \putenv('THEME_MODE=PROD');
        $this->assertSame(Loader::THEME_MODE_PROD, (new Loader())->get_theme_mode());

        //Not supported, should fall back to dev
        \putenv('THEME_MODE=CHEESE');
        $this->assertSame(Loader::THEME_MODE_DEV, (new Loader())->get_theme_mode());
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_theme_css_mode
     */
    public function test___get_theme_css_mode()
    {
        $this->assertSame(Loader::THEME_MODE_ASSET_DYNAMIC, (new Loader())->get_theme_css_mode());

        \putenv('THEME_CSS_MODE=static');
        $this->assertSame(Loader::THEME_MODE_ASSET_STATIC, (new Loader())->get_theme_css_mode());

        \putenv('THEME_CSS_MODE=dynamic');
        $this->assertSame(Loader::THEME_MODE_ASSET_DYNAMIC, (new Loader())->get_theme_css_mode());
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_theme_css_mode
     */
    public function test___get_theme_css_mode__production()
    {
        //Make sure these aren't set
        $this->assertFalse(\getenv('THEME_MODE'));
        $this->assertFalse(\getenv('THEME_CSS_MODE'));

        //In production mode, it is always static
        \putenv('THEME_MODE=prod');
        $this->assertSame(Loader::THEME_MODE_ASSET_STATIC, (new Loader())->get_theme_css_mode());

        //Even if someone sets this, the production mode above always wins
        \putenv('THEME_CSS_MODE=dynamic');
        $this->assertSame(Loader::THEME_MODE_ASSET_STATIC, (new Loader())->get_theme_css_mode());
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_theme_js_mode
     */
    public function test___get_theme_js_mode()
    {
        $this->assertSame(Loader::THEME_MODE_ASSET_DYNAMIC, (new Loader())->get_theme_js_mode());

        \putenv('THEME_JS_MODE=static');
        $this->assertSame(Loader::THEME_MODE_ASSET_STATIC, (new Loader())->get_theme_js_mode());

        \putenv('THEME_JS_MODE=dynamic');
        $this->assertSame(Loader::THEME_MODE_ASSET_DYNAMIC, (new Loader())->get_theme_js_mode());
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_theme_js_mode
     */
    public function test___get_theme_js_mode__production()
    {
        //Make sure these aren't set
        $this->assertFalse(\getenv('THEME_MODE'));
        $this->assertFalse(\getenv('THEME_JS_MODE'));

        //In production mode, it is always static
        \putenv('THEME_MODE=prod');
        $this->assertSame(Loader::THEME_MODE_ASSET_STATIC, (new Loader())->get_theme_js_mode());

        //Even if someone sets this, the production mode above always wins
        \putenv('THEME_JS_MODE=dynamic');
        $this->assertSame(Loader::THEME_MODE_ASSET_STATIC, (new Loader())->get_theme_js_mode());
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_media_dir
     */
    public function test__get_media_dir()
    {
        global $current_test_dir;
        $current_test_dir = '/cheese/';
        $this->assertSame('/cheese', ((new Loader())->get_media_dir()));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_media_url
     */
    public function test__get_media_url()
    {
        global $current_test_url;
        $current_test_url = '/cheese/';
        $this->assertSame('/cheese', ((new Loader())->get_media_url()));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_webpack_entry_file
     */
    public function test__get_webpack_entry_file()
    {
        global $current_test_dir;
        $current_test_dir = '/cheese/';
        $this->assertSame('/cheese/static/build/entrypoints.json', ((new Loader())->get_webpack_entry_file()));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_webpack_entry_file
     */
    public function test__get_webpack_entry_file__from_env()
    {
        global $current_test_dir;
        $current_test_dir = '/cheese/';

        //This is absolute and will ignore the media_dir
        \putenv('THEME_WEBPACK_ENTRY_FILE=/tmp/test.json');
        $this->assertSame('/tmp/test.json', ((new Loader())->get_webpack_entry_file()));

        //This is relative and will use the media_dir
        \putenv('THEME_WEBPACK_ENTRY_FILE=./tmp/test.json');
        $this->assertSame('/cheese/tmp/test.json', ((new Loader())->get_webpack_entry_file()));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_webpack_entry_file
     */
    public function test__get_webpack_entry_file__exists()
    {
        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");
        \touch($entry_file);

        $this->assertSame($entry_file, ((new Loader())->get_webpack_entry_file()));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_webpack_default_entry_name
     */
    public function test__get_webpack_default_entry_name()
    {
        $this->assertSame('main', ((new Loader())->get_webpack_default_entry_name()));

        \putenv('THEME_WEBPACK_ENTRY_DEFAULT=cheese');
        $this->assertSame('cheese', ((new Loader())->get_webpack_default_entry_name()));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_entries_by_name
     */
    public function test__get_entries_by_name()
    {
        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = <<<EOT
        {
            "entrypoints": {
                "main": {
                    "js": [
                        "/runtime.2e9ebe81.js",
                        "/main.5b014969.js"
                    ],
                    "css": [
                        "/main.add76d32.css"
                    ]
                }
            }
        }
EOT;

        \file_put_contents($entry_file, $test_data);

        $entries = (new Loader())->get_entries_by_name('main');

        $this->assertInternalType('array', $entries);
        $this->assertArrayHasKey('js', $entries);
        $this->assertArrayHasKey('css', $entries);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_entries_by_name
     */
    public function test__get_entries_by_name__no_file_exception()
    {
        global $current_test_dir;
        $current_test_dir = $this->get_root_dir_name_no_trailing_slash();

        $this->expectException(\Exception::class);
        (new Loader())->get_entries_by_name('main');
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_entries_by_name
     */
    public function test__get_entries_by_name__invalid_json_exception()
    {
        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = 'BAD DATA';

        \file_put_contents($entry_file, $test_data);

        $this->expectException(\Exception::class);
        (new Loader())->get_entries_by_name('main');
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::get_entries_by_name
     */
    public function test__get_entries_by_name__nothing_found()
    {
        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = <<<EOT
        {
            "entrypoints": {
                "main": {
                    "js": [
                        "/runtime.2e9ebe81.js",
                        "/main.5b014969.js"
                    ],
                    "css": [
                        "/main.add76d32.css"
                    ]
                }
            }
        }
EOT;

        \file_put_contents($entry_file, $test_data);

        $entries = (new Loader())->get_entries_by_name('cheese');

        $this->assertInternalType('array', $entries);
        $this->assertCount(0, $entries);
    }
}
