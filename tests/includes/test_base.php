<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
class test_base extends TestCase
{
    //This is name of our FS root for testing
    private $_test_root_name = 'vendi-asset-loader-test';

    //This is an instance of the Virtual File System
    private $_root;

    public function get_vfs_root()
    {
        if (!$this->_root) {
            $this->_root = vfsStream::setup(
                                            $this->get_root_dir_name_no_trailing_slash(),
                                            null,
                                            []
                                        );
        }
        return $this->_root;
    }

    public function get_root_dir_name_no_trailing_slash()
    {
        return $this->_test_root_name;
    }

    public function setUp()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;
        global $vendi_asset_scripts;
        global $apply_filters_function;

        $current_test_dir = null;
        $current_test_url = null;
        $vendi_asset_styles = null;
        $vendi_asset_scripts = null;
        $apply_filters_function = null;

        $this->get_vfs_root();
    }

    public function tearDown()
    {
        global $current_test_dir;
        global $current_test_url;
        global $vendi_asset_styles;
        global $vendi_asset_scripts;
        global $apply_filters_function;

        $current_test_dir = null;
        $current_test_url = null;
        $vendi_asset_styles = null;
        $vendi_asset_scripts = null;
        $apply_filters_function = null;
    }

}
