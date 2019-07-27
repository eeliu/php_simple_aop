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
 * Date: 2/13/19
 * Time: 5:32 PM
 */

namespace pinpoint\Common;
use pinpoint\Common\Util;

class ClassMap
{
    public $classMap=[];

    public function __construct($file=null)
    {
        if($file)
        {
            $str = file_get_contents($file);
            $this->classMap = unserialize($str);
        }
    }

    public function insertMapping($cl,$file)
    {
        $this->classMap[$cl] = $file;
    }

    public function persistenceClassMapping($file)
    {
        $context = serialize($this->classMap);
        Util::flushStr2File($context,$file);
    }
    public function debug()
    {
        print_r($this->classMap);
    }
}