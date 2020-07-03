<?php
/**
 * Copyright 2020-present NAVER Corp.
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

namespace pinpoint;
use pinpoint\Common\AopClassMap;
use pinpoint\Common\PinpointDriver;

$classMap = null;
if(defined('USER_DEFINED_CLASS_MAP_IMPLEMENT'))
{
    $className = USER_DEFINED_CLASS_MAP_IMPLEMENT;
    $classMap = new $className();
    assert($classMap instanceof AopClassMap);
}else{
    $classMap = new AopClassMap();
}

PinpointDriver::getInstance()->init($classMap);

if(class_exists("\Plugins\PerRequestPlugins")){
    \Plugins\PerRequestPlugins::instance();
}
