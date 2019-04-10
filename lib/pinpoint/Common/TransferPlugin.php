<?php
/**
 * User: eeliu
 * Date: 2/3/19
 * Time: 11:29 AM
 */

namespace pinpoint\Common;
use pinpoint\Common\OriginClass1;
use pinpoint\Common\ClassShadow;
use pinpoint\Common\PluginParser;

class TransferPlugin
{
    /*
     * {
     * "className1"=>( "file"=>$path),
     * "className2"=>( "file"=>$path)
     * }
     */
    private $classMap;
    public function __construct()
    {
    }

    public function renderPlugins($plugins)
    {
        if(!$plugins instanceof PluginParser)
        {
            throw new \Exception("");
        }


    }

}