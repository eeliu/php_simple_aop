<?php

namespace pinpoint;
require AUTOLOAD_FILE_ALIAS;

use pinpoint\Common\PinpointDriver;

PinpointDriver::getInstance()->init();
$oncePlugins = null;
//todo add TSpan plugins
if(class_exists("\Plugins\RequestOncePlugins")){
    $oncePlugins = new \Plugins\RequestOncePlugins();
}
