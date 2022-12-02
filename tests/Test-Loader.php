<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Loader;
use Symfony\Component\Filesystem\Path;

class Test_Loader extends test_base
{
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
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_css
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_css_dynamic
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

        $this->assertIsArray($vendi_asset_styles);
        $this->assertCount(2, $vendi_asset_styles);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_js
     * @covers \Vendi\VendiAssetLoader\Loader::enqueue_js_dynamic
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

        $this->assertIsArray($vendi_asset_scripts);
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
