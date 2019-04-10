<?php
/**
 * @todo
 * This draft file is intended for internal func, try to support in next version
 */

namespace pinpoint\Common;


class WrapperInternalClass
{
    public static $originName ='PDO';
    private $objInstance;
    public function __construct(...$args)
    {
        $oReflectionClass = new ReflectionClass('PDO');
        $this->objInstance = $oReflectionClass->newInstance(...$args);
    }

    public function __call($name, $arguments)
    {

        return call_user_func_array(array($this->objInstance,$name),$arguments);
    }

    public static  function __callStatic($name, $args)
    {
        try
        {

            $ret = call_user_func_array(array(self::$originName,$name),$args);
        }catch (Exception $e)
        {
            throw new \Exception($name." not find");
        }

        return $ret;
    }
}