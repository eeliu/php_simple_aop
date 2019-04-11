<?php
/**
 * User: eeliu
 * Date: 4/1/19
 * Time: 3:27 PM
 */

namespace pinpoint\test;
require_once __DIR__. '/../../vendor/autoload.php';

use pinpoint\Common\GenRequiredBIFile;
use PHPUnit\Framework\TestCase;

class GenRequiredBIFileTest extends TestCase
{

    public function testLoadToFile()
    {
        $bi = new GenRequiredBIFile("app\Foo");
        $bi->extendsMethod("PDO","query",[7,'pinpiont','commPlugins']);
        $bi->extendsMethod("PDO","exec",[3,'pinpiont','testPlugins']);
        $bi->extendsMethod("Exception","__toString",[3,'pinpiont','testPlugins']);

        $bi->extendsFunc("array_push",[7,'pinpiont','commPlugins']);
        $bi->extendsFunc("curl_init",[7,'pinpiont','curlPlugins']);
        $bi->extendsFunc("curl_setopt",[7,'pinpiont','curlSetoptPlugins']);

        $bi->loadToFile("./required.php");
    }
}

