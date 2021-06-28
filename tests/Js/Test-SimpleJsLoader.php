<?php

declare(strict_types=1);

namespace Vendi\VendiAssetLoader\tests;

use org\bovigo\vfs\vfsStream;
use Vendi\VendiAssetLoader\Js\SimpleJsLoader;
use Webmozart\PathUtil\Path;

class Test_SimpleJsLoader extends test_base
{
    private function _get_obj_for_testing()
    {
        return new SimpleJsLoader();
    }

    /**
     * @covers \Vendi\VendiAssetLoader\Js\SimpleJsLoader::enqueue_files
     */
    public function test_enqueue_files()
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

        $files = ['000-test' => true, '100-test' => true];

        //Dir has two files that match pattern
        $obj = $this->_get_obj_for_testing();
        $this->assertSame(2, $obj->enqueue_files());
        $this->assertCount(2, $vendi_asset_scripts);

        foreach ($files as $key => $in_footer) {
            $this->assertArrayHasKey($key . '-script', $vendi_asset_scripts);
            $this->assertIsArray($vendi_asset_scripts[$key . '-script']);
            $sub = $vendi_asset_scripts[$key . '-script'];
            $this->assertCount(5, $sub);
            $this->assertSame($key . '-script', array_shift($sub));
            $this->assertSame('http://www.example.net/js/' . $key . '.js', array_shift($sub));
            $this->assertNull(array_shift($sub));
            $this->assertIsInt(array_shift($sub));
            $this->assertSame($in_footer, array_shift($sub));
            $this->assertEmpty($sub);
        }
    }
}
