<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\CommonLoaderBase;
use Webmozart\PathUtil\Path;

class Test_CommonLoaderBase extends test_base
{
    private function _get_obj_for_testing()
    {
        return new class extends CommonLoaderBase {
            //We don't care about this here
            public function enqueue_files() : int
            {
                return -1;
            }

            public function get_enqueue_function_for_specific_type() : callable
            {
                return function () {
                };
            }

            public function get_extension_for_specific_type() : string
            {
                return '';
            }

            public function get_handle_suffix_for_specific_type() : string
            {
                return '';
            }
        }
                ;
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::_get_dir_and_url_tuple
     */
    public function test___get_dir_and_url_tuple()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $obj = $this->_get_obj_for_testing();

        $ret = $obj->_get_dir_and_url_tuple('cheese');

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('media_dir', $ret);
        $this->assertArrayHasKey('media_url', $ret);
        $this->assertSame('http://www.example.net/cheese', $ret['media_url']);
        $this->assertSame('vfs://vendi-asset-loader-test/cheese', $ret['media_dir']);

        unset($ret['media_dir']);
        unset($ret['media_url']);

        $this->assertEmpty($ret);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::_get_dir_and_url_tuple
     */
    public function test___get_dir_and_url_tuple__extra_param()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $obj = $this->_get_obj_for_testing();

        $ret = $obj->_get_dir_and_url_tuple('cheese', 'beta');

        $this->assertInternalType('array', $ret);
        $this->assertArrayHasKey('media_dir', $ret);
        $this->assertArrayHasKey('media_url', $ret);
        $this->assertSame('http://www.example.net/cheese/beta', $ret['media_url']);
        $this->assertSame('vfs://vendi-asset-loader-test/cheese/beta', $ret['media_dir']);

        unset($ret['media_dir']);
        unset($ret['media_url']);

        $this->assertEmpty($ret);
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::get_files
     */
    public function test__get_files__invalid_type()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $obj = $this->_get_obj_for_testing();

        //This type is not valid
        $this->expectException(\Exception::class);
        $obj->get_files('cheese');
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::get_files
     */
    public function test__get_files__no_dir()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        //Dir does not exist, should return null
        $obj = $this->_get_obj_for_testing();
        $this->assertNull($obj->get_files('css'));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::get_files
     */
    public function test__get_files()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        mkdir($css_folder_abs);
        touch(Path::join($css_folder_abs, '000-test.css'));
        touch(Path::join($css_folder_abs, '100-test.css'));
        touch(Path::join($css_folder_abs, '100-test.js'));
        touch(Path::join($css_folder_abs, 'test.css'));

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertCount(2, $obj->get_files('css'));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::get_files
     */
    public function test__get_files__extra_param()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        $css_subfolder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css', 'print'));
        mkdir($css_folder_abs);
        mkdir($css_subfolder_abs);

        //This file is one folder up, it shouldn't be found
        touch(Path::join($css_folder_abs, '000-test.css'));
        touch(Path::join($css_subfolder_abs, '000-test.css'));
        touch(Path::join($css_subfolder_abs, '100-test.css'));
        touch(Path::join($css_subfolder_abs, '100-test.js'));
        touch(Path::join($css_subfolder_abs, 'test.css'));

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertCount(2, $obj->get_files('css', 'print'));
    }

    /**
     * @covers \Vendi\VendiAssetLoader\CommonLoaderBase::get_files
     */
    public function test__get_files__empty()
    {
        global $current_test_dir;
        global $current_test_url;

        $current_test_url = 'http://www.example.net/';
        $current_test_dir = vfsStream::url($this->get_root_dir_name_no_trailing_slash());

        $css_folder_abs = vfsStream::url(Path::join($this->get_root_dir_name_no_trailing_slash(), 'css'));
        mkdir($css_folder_abs);

        //Dir empty, should return null
        $obj = $this->_get_obj_for_testing();
        $this->assertNull($obj->get_files('css'));
    }
}
