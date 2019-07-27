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