<?php

namespace pinpoint;
//require AUTOLOAD_FILE_ALIAS;

use pinpoint\Common\PinpointDriver;

PinpointDriver::getInstance()->init();

if(class_exists("\Plugins\PerRequestPlugins")){
    \Plugins\PerRequestPlugins::instance();
}
