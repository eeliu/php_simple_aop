<?php
/**
 * User: eeliu
 * Date: 4/1/19
 * Time: 3:27 PM
 */

namespace pinpoint\test;

require_once 'bootstrap.php';

use pinpoint\Common\GenRequiredBIFile;
use PHPUnit\Framework\TestCase;

/**
 * Class GenRequiredBIFileTest
 * Test built-in class /Function
 * @package pinpoint\test
 */
class GenRequiredBIFileTest extends TestCase
{

    public function testLoadToFile()
    {
        $bi = new GenRequiredBIFile("app\Foo");
        $bi->extendsMethod("PDO","query",[7,'pinpoint','commPlugins']);
        $bi->extendsMethod("PDO","prepare",[7,'pinpoint','commPlugins']);
        $bi->extendsMethod("PDO","rollBack",[7,'pinpoint','commPlugins']);
        $bi->extendsMethod("PDO","lastInsertId",[7,'pinpoint','commPlugins']);
        $bi->extendsMethod("PDO","exec",[7,'pinpoint','testPlugins']);
        $bi->extendsMethod("PDOStatement","fetchAll",[7,'pinpoint','testPlugins']);
        $bi->extendsMethod("Exception","__toString",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("Redis","connect",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("Redis","hGet",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("Redis","ttl",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("Redis","hDel",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("Redis","del",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("Redis","hGetAll",[3,'pinpoint','testPlugins']);
        $bi->extendsMethod("mysqli_result","fetch_all",[3,'pinpoint','testPlugins']);

        $bi->extendsFunc("array_push",[7,'pinpoint','commPlugins']);
        $bi->extendsFunc("array_merge",[7,'pinpoint','commPlugins']);
        $bi->extendsFunc("curl_init",[7,'pinpoint','curlPlugins']);
        $bi->extendsFunc("curl_setopt",[7,'pinpoint','curlSetoptPlugins']);
        $bi->extendsFunc("date",[7,'pinpoint','curlSetoptPlugins']);

        $bi->loadToFile("required_test.php");
        self::assertFileExists("required_test.php");
        self::assertFileEquals("required.php","required_test.php");
        unlink("required_test.php");
    }
}

