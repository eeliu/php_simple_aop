<?php
/**
 * User: eeliu
 * Date: 2/1/19
 * Time: 4:21 PM
 */

namespace pinpoint\Common;
use Composer\Autoload\ClassLoader;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;
class Util
{
    private static $origin_class_loader;
    const StartWith = '\/\/\/@hook:';
    const Method= 1;
    const Function= 2;

    /** locate a class (via vendor aop)
     * @param $class
     * @return bool|string
     */
    public static function findFile($class)
    {
        if (is_null(Util::$origin_class_loader)) {
            $loaders = spl_autoload_functions();
            foreach ($loaders as $loader) {
                if (is_array($loaders) && $loader[0] instanceof ClassLoader) {
                    Util::$origin_class_loader = $loader[0];
                    break;
                }
            }
        }
        $address = Util::$origin_class_loader->findFile($class);
        if($address){
            return realpath($address);
        }
        return false;
    }

    public static function parseUserFunc($str)
    {
        if (preg_match('/^' . self::StartWith . './', $str)) {
            return preg_split("/(" . self::StartWith . ")| /", $str, -1, PREG_SPLIT_NO_EMPTY);
        }
        return [];
    }

    public static function flushStr2File(&$context, $fullPath)
    {
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($fullPath, $context);
    }

    public static function isBuiltIn($name)
    {
        if (strpos($name, "\\") === 0) //build-in
        {
            if (strpos($name, "::") > 0){
                return static::Method;
            }else{
                return static::Function;
            }
        }

        return null;
    }

}
