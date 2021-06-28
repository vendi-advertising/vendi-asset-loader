<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Css\TypedCssLoader;
use Webmozart\PathUtil\Path;

class Test_TypedCssLoader extends test_base
{
    private function _get_obj_for_testing()
    {
        return new TypedCssLoader();
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Css\TypedCssLoader::enqueue_files
     */
    public function test_enqueue_files()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        $css_screen_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css', 'screen'));
        $css_print_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css', 'print'));
        $css_cheese_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css', 'cheese'));

        mkdir($css_folder_abs);
        mkdir($css_screen_folder_abs);
        mkdir($css_print_folder_abs);
        mkdir($css_cheese_folder_abs);

        touch(Path::join($css_screen_folder_abs, '000-test.css'));
        touch(Path::join($css_print_folder_abs, '100-test.css'));
        touch(Path::join($css_cheese_folder_abs, '200-test.css'));

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(2, $obj->enqueue_files());
        $this->assertCount(2, $vendi_asset_styles);

        foreach (['000-test' => 'screen', '100-test' => 'print'] as $key => $type) {
            $this->assertArrayHasKey($key . '-style', $vendi_asset_styles);
            $this->assertIsArray($vendi_asset_styles[$key . '-style']);
            $sub = $vendi_asset_styles[$key . '-style'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-style', array_shift($sub));
            $this->assertSame('http://www.example.net/css/' . $type . '/' . $key . '.css', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertIsInt(array_shift($sub));
            $this->assertSame($type, array_shift($sub));
            $this->assertEmpty($sub);
        }
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Css\TypedCssLoader::enqueue_files
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
