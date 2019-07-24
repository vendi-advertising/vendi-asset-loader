<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Loader;
use Webmozart\PathUtil\Path;

class Test_Loader extends test_base
{
    public function test__get_env()
    {
        $this->assertFalse(\getenv('THEME_MODE'));
        $this->assertSame('', (new Loader())->get_env('THEME_MODE'));
        \putenv('THEME_MODE=dev');
        $this->assertSame('dev', (new Loader())->get_env('THEME_MODE'));
    }

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

        $test_data = $this->get_test_json();

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

        $test_data = $this->get_test_json();

        \file_put_contents($entry_file, $test_data);

        $entries = (new Loader())->get_entries_by_name('cheese');

        $this->assertInternalType('array', $entries);
        $this->assertCount(0, $entries);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_css
     */
    public function test__enqueue_css__production()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());
        $current_test_url = 'https://example/';

        \putenv('THEME_MODE=prod');

        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = $this->get_test_json();

        \file_put_contents($entry_file, $test_data);

        (new Loader())->enqueue_css('main');

        global $vendi_asset_styles;

        $this->assertInternalType('array', $vendi_asset_styles);
        $this->assertArrayHasKey('main.add76d32-style', $vendi_asset_styles);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_css
     */
    public function test__enqueue_css__production__default_entry()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());
        $current_test_url = 'https://example/';

        \putenv('THEME_MODE=prod');

        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = $this->get_test_json();

        \file_put_contents($entry_file, $test_data);

        (new Loader())->enqueue_css();

        global $vendi_asset_styles;

        $this->assertInternalType('array', $vendi_asset_styles);
        $this->assertArrayHasKey('main.add76d32-style', $vendi_asset_styles);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_js
     */
    public function test__enqueue_js__production()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());
        $current_test_url = 'https://example/';

        \putenv('THEME_MODE=prod');

        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = $this->get_test_json();

        \file_put_contents($entry_file, $test_data);

        (new Loader())->enqueue_js('main');

        global $vendi_asset_scripts;

        $this->assertInternalType('array', $vendi_asset_scripts);
        $this->assertArrayHasKey('runtime.2e9ebe81-script', $vendi_asset_scripts);
        $this->assertArrayHasKey('main.5b014969-script', $vendi_asset_scripts);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_js
     */
    public function test__enqueue_js__production__default_entry()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());
        $current_test_url = 'https://example/';

        \putenv('THEME_MODE=prod');

        $entry_file = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'entry.json'));
        \putenv("THEME_WEBPACK_ENTRY_FILE=${entry_file}");

        $test_data = $this->get_test_json();

        \file_put_contents($entry_file, $test_data);

        (new Loader())->enqueue_js();

        global $vendi_asset_scripts;

        $this->assertInternalType('array', $vendi_asset_scripts);
        $this->assertArrayHasKey('runtime.2e9ebe81-script', $vendi_asset_scripts);
        $this->assertArrayHasKey('main.5b014969-script', $vendi_asset_scripts);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_css
     */
    public function test__enqueue_css__dev()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));

        \mkdir($css_folder_abs);
        \touch(Path::join($css_folder_abs, '000-test.css'));
        \touch(Path::join($css_folder_abs, '100-test.css'));
        \touch(Path::join($css_folder_abs, '100-test.js'));
        \touch(Path::join($css_folder_abs, 'test.css'));

        \putenv('THEME_MODE=dev');

        (new Loader())->enqueue_css();

        global $vendi_asset_styles;

        $this->assertInternalType('array', $vendi_asset_styles);
        $this->assertCount(2, $vendi_asset_styles);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_js
     */
    public function test__enqueue_js__dev()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $js_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js'));

        \mkdir($js_folder_abs);
        \touch(Path::join($js_folder_abs, '000-test.css'));
        \touch(Path::join($js_folder_abs, '100-test.css'));
        \touch(Path::join($js_folder_abs, '100-test.js'));
        \touch(Path::join($js_folder_abs, 'test.css'));

        \putenv('THEME_MODE=dev');

        (new Loader())->enqueue_js();

        global $vendi_asset_scripts;

        $this->assertInternalType('array', $vendi_asset_scripts);
        $this->assertCount(1, $vendi_asset_scripts);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_default
     */
    public function test__enqueue_default()
    {
        global $current_test_dir;
        global $current_test_url;
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());
        $current_test_url = 'https://example/';

        //TODO: This is a really bad thing. It invokes code but isn't testing anything yet
        Loader::enqueue_default();

        global $vendi_asset_scripts;
        global $vendi_asset_styles;

        $this->assertNull($vendi_asset_scripts);
        $this->assertNull($vendi_asset_scripts);
    }
}
