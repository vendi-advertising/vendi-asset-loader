<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Css\CssLoaderBase;
use Symfony\Component\Filesystem\Path;

class Test_CssLoaderBase extends test_base
{
    private function _get_obj_for_testing()
    {
        return new class extends CssLoaderBase {
            //We don't care about this here
            public function enqueue_files(): int
            {
                return -1;
            }
        };
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Css\CssLoaderBase::enqueue_files_with_optional_type
     * @covers \Vendi\VendiAssetLoader\Css\CssLoaderBase::actually_enqueue_files
     */
    public function test_enqueue_files_with_optional_type()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        mkdir($css_folder_abs);
        touch(Path::join($css_folder_abs, '000-test.css'));
        touch(Path::join($css_folder_abs, '100-test.css'));

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(2, $obj->enqueue_files_with_optional_type());
        $this->assertCount(2, $vendi_asset_styles);

        foreach (['000-test', '100-test'] as $key) {
            $this->assertArrayHasKey($key . '-style', $vendi_asset_styles);
            $this->assertIsArray($vendi_asset_styles[$key . '-style']);
            $sub = $vendi_asset_styles[$key . '-style'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-style', array_shift($sub));
            $this->assertSame('http://www.example.net/css/' . $key . '.css', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertIsInt(array_shift($sub));
            $this->assertSame('screen', array_shift($sub));
            $this->assertEmpty($sub);
        }
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Css\CssLoaderBase::enqueue_files_with_optional_type
     */
    public function test_enqueue_files_with_optional_type__empty_dir()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        mkdir($css_folder_abs);

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(0, $obj->enqueue_files_with_optional_type());
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Css\CssLoaderBase::enqueue_files_with_optional_type
     * @covers \Vendi\VendiAssetLoader\Css\CssLoaderBase::actually_enqueue_files
     */
    public function test_enqueue_files_with_optional_type__with_type()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        $css_subfolder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css', 'screen'));
        mkdir($css_folder_abs);
        mkdir($css_subfolder_abs);
        touch(Path::join($css_subfolder_abs, '000-test.css'));
        touch(Path::join($css_subfolder_abs, '100-test.css'));

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(2, $obj->enqueue_files_with_optional_type('screen'));
        $this->assertCount(2, $vendi_asset_styles);

        foreach (['000-test', '100-test'] as $key) {
            $this->assertArrayHasKey($key . '-style', $vendi_asset_styles);
            $this->assertIsArray($vendi_asset_styles[$key . '-style']);
            $sub = $vendi_asset_styles[$key . '-style'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-style', array_shift($sub));
            $this->assertSame('http://www.example.net/css/screen/' . $key . '.css', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertIsInt(array_shift($sub));
            $this->assertSame('screen', array_shift($sub));
            $this->assertEmpty($sub);
        }
    }
}
