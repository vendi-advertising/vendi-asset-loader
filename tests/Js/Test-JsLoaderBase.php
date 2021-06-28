<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Js\JsLoaderBase;
use Webmozart\PathUtil\Path;

class Test_JsLoaderBase extends test_base
{
    private function _get_obj_for_testing()
    {
        return new class extends JsLoaderBase {
            //We don't care about this here
            public function enqueue_files(): int
            {
                return -1;
            }
        };
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Js\JsLoaderBase::enqueue_files_with_optional_high_low
     * @covers \Vendi\VendiAssetLoader\Js\JsLoaderBase::actually_enqueue_files
     */
    public function test_enqueue_files_with_optional_high_low()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_scripts;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $js_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js'));
        mkdir($js_folder_abs);
        touch(Path::join($js_folder_abs, '000-test.js'));
        touch(Path::join($js_folder_abs, '100-test.js'));

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(2, $obj->enqueue_files_with_optional_high_low(true));
        $this->assertCount(2, $vendi_asset_scripts);

        foreach (['000-test', '100-test'] as $key) {
            $this->assertArrayHasKey($key . '-script', $vendi_asset_scripts);
            $this->assertIsArray($vendi_asset_scripts[$key . '-script']);
            $sub = $vendi_asset_scripts[$key . '-script'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-script', array_shift($sub));
            $this->assertSame('http://www.example.net/js/' . $key . '.js', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertIsInt(array_shift($sub));
            $this->assertTrue(array_shift($sub));
            $this->assertEmpty($sub);
        }
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Js\JsLoaderBase::enqueue_files_with_optional_high_low
     */
    public function test_enqueue_files_with_optional_high_low__empty_dir()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_scripts;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $js_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js'));
        mkdir($js_folder_abs);

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(0, $obj->enqueue_files_with_optional_high_low(true));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Js\JsLoaderBase::enqueue_files_with_optional_high_low
     * @covers \Vendi\VendiAssetLoader\Js\JsLoaderBase::actually_enqueue_files
     */
    public function test_enqueue_files_with_optional_high_low__with_type()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_scripts;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $js_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js'));
        $js_header_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js', 'header'));
        $js_footer_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'js', 'footer'));
        mkdir($js_folder_abs);
        mkdir($js_header_folder_abs);
        mkdir($js_footer_folder_abs);
        touch(Path::join($js_header_folder_abs, '000-test.js'));
        touch(Path::join($js_footer_folder_abs, '100-test.js'));

        $files = ['000-test' => ['in_footer' => false, 'folder' => 'header'], '100-test' => ['in_footer' => true, 'folder' => 'footer']];

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(1, $obj->enqueue_files_with_optional_high_low(false, 'header'));
        $this->assertSame(1, $obj->enqueue_files_with_optional_high_low(true, 'footer'));
        $this->assertCount(2, $vendi_asset_scripts);

        foreach ($files as $key => $more) {
            $in_footer = $more['in_footer'];
            $folder = $more['folder'];

            $this->assertArrayHasKey($key . '-script', $vendi_asset_scripts);
            $this->assertIsArray($vendi_asset_scripts[$key . '-script']);
            $sub = $vendi_asset_scripts[$key . '-script'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-script', array_shift($sub));
            $this->assertSame('http://www.example.net/js/' . $folder . '/' . $key . '.js', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertIsInt(array_shift($sub));
            $this->assertSame($in_footer, array_shift($sub));
            $this->assertEmpty($sub);
        }
    }
}
