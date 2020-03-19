<?php
/**
 * Copyright 2019 NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace pinpoint\Common;

use Composer\Autoload\ClassLoader;

class AopClassLoader
{
    public  static $inInitalized;
    private $origin; //  origin classloader
    private $classMap;
    public function __construct($origin, $classMap)
    {
        $this->classMap = $classMap;

        $this->origin = $origin;
    }

    public function findFile($class)
    {
        $file = isset($this->classMap[$class]) ?  $this->classMap[$class]: null;

        if( is_null($file) ) {
            $file = $this->origin->findFile($class);
            if ($file !== false)
            {
                $file = realpath($file) ?: $file;
                $this->classMap[$class] = $file;
            }
        }
        return $file;
    }

    public function loadClass($class)
    {
        $file = $this->findFile($class);

        if ($file !== false) {
            include $file;
        }
    }

    /**
     * register pinpoint aop class loader, wrapped vendor class loader
     * @param $classIndex
     * @return bool
     */
    public static  function init($classIndex)
    {
        $loaders = spl_autoload_functions();
        foreach ($loaders as &$loader) {
            $loaderToUnregister = $loader;
            if (is_array($loader) && ($loader[0] instanceof ClassLoader)) {
                spl_autoload_unregister($loaderToUnregister);
                $originalLoader = $loader[0];
                $loader[0] = new AopClassLoader($loader[0],$classIndex);
                spl_autoload_register($loader,true,true);
                self::$inInitalized = true;
            }
        }

        return self::$inInitalized;

    }

}
