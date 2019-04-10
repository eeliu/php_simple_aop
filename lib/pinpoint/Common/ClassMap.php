<?php
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