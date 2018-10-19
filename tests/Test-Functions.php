<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Webmozart\PathUtil\Path;
use function Vendi\VendiAssetLoader\load_simple_assets;
use function Vendi\VendiAssetLoader\load_sorted_js;
use function Vendi\VendiAssetLoader\load_typed_css;

class Test_Functions extends test_base
{
    public function test__load_simple_assets()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_scripts;
        global $vendi_asset_styles;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        $js_folder_abs  = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js'));

        mkdir($css_folder_abs);
        mkdir($js_folder_abs);

        touch(Path::join($css_folder_abs, '000-test.css'));
        touch(Path::join($js_folder_abs, '000-test.js'));

        load_simple_assets();

        $this->assertCount(1, $vendi_asset_styles);
        $this->assertCount(1, $vendi_asset_scripts);
    }

    public function test__load_typed_css()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        $css_sub_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css', 'screen'));

        mkdir($css_folder_abs);
        mkdir($css_sub_folder_abs);

        touch(Path::join($css_sub_folder_abs, '000-test.css'));

        load_typed_css();

        $this->assertCount(1, $vendi_asset_styles);
    }

    public function test__load_sorted_js()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_scripts;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $js_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js'));
        $js_sub_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js', 'footer'));

        mkdir($js_folder_abs);
        mkdir($js_sub_folder_abs);

        touch(Path::join($js_sub_folder_abs, '000-test.js'));

        load_sorted_js();

        $this->assertCount(1, $vendi_asset_scripts);
    }
}
