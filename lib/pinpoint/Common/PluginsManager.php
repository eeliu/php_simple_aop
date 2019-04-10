<?php

namespace pinpoint\Common;
use pinpoint\Common\PluginParser;

/**
 * @deprecated
*/
class PluginsManager
{
    /*
     * (
     *  class           function
     *  'app\foo':('func1'=>[mode] = 1,'func2'=>[mode] = 1,'func3'=>[mode] = 2) \\ func3 system function
     * )
     */
    private $UserPluginsInfo = array();

    public function registerPlugins($plugins)
    {
        foreach ($plugins as $plugin)
        {
            assert($plugin instanceof PluginParser);
            $tPlugins = $plugin->getClArray();
            foreach ($tPlugins as $cl=>$detail)
            {
                if(!array_key_exists($cl ,$this->UserPluginsInfo))
                {
                    $this->UserPluginsInfo[$cl] = $detail;
                }elseif (!array_key_exists($this->UserPluginsInfo[$cl]))
                {

                }

            }



        }



    }
}