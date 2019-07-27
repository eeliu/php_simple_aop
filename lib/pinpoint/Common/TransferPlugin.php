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