<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Js\SortedJsLoader;
use Webmozart\PathUtil\Path;

class Test_SortedJsLoader extends test_base
{
    private function _get_obj_for_testing()
    {
        return new SortedJsLoader();
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Js\SortedJsLoader::enqueue_files
     */
    public function test_enqueue_files()
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
        $this->assertSame(2, $obj->enqueue_files());
        $this->assertCount(2, $vendi_asset_scripts);

        foreach ($files as $key => $more) {
            $in_footer = $more['in_footer'];
            $folder = $more['folder'];

            $this->assertArrayHasKey($key . '-script', $vendi_asset_scripts);
            $this->assertInternalType('array', $vendi_asset_scripts[$key . '-script']);
            $sub = $vendi_asset_scripts[$key . '-script'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-script', array_shift($sub));
            $this->assertSame('http://www.example.net/js/' . $folder . '/' . $key . '.js', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertInternalType('integer', array_shift($sub));
            $this->assertSame($in_footer, array_shift($sub));
            $this->assertEmpty($sub);
        }
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Js\SortedJsLoader::enqueue_files
     */
    public function test_enqueue_files__invalid_type()
    {
        global $apply_filters_function;
        $apply_filters_function = function () {
            return null;
        };

        $obj = $this->_get_obj_for_testing();

        //This type is not valid
        $this->expectException(\Exception::class);

        $obj->enqueue_files();
    }
}
