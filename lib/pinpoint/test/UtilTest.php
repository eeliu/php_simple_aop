<?php
/**
 * User: eeliu
 * Date: 2/1/19
 * Time: 4:27 PM
 */

namespace pinpoint\test;
require_once 'bootstrap.php';

use PHPUnit\Framework\TestCase;
use pinpoint\Common\Util;
use Plugins\CommonPlugin;
//use app\Foo;

class UtilTest extends TestCase
{

    public function testFindFile()
    {
        self::assertEquals(Util::findFile(UtilTest::class),__FILE__);
        self::assertStringEndsWith('php', Util::findFile(CommonPlugin::class));
        self::assertFalse(Util::findFile("xx\Foo"));
    }

    public function testparseUserFunc()
    {
        $str = '///@hook:\app\Foo::foo_p2 \app\Foo::foo_p1';

        self::assertEquals(serialize(Util::parseUserFunc($str)),serialize(['\app\Foo::foo_p2','\app\Foo::foo_p1']));

//        self::assertEquals(Util::parseUserFunc($str),['\app\Foo::foo_p2','\app\Foo::foo_p1']);
        self::assertEquals(count(Util::parseUserFunc('\app\Foo::foo_p2 \app\Foo::foo_p1')),0);
        self::assertEquals(count(Util::parseUserFunc('')),0);
        self::assertEquals(count(Util::parseUserFunc('I don\'t now nothing')),0);
    }
}
