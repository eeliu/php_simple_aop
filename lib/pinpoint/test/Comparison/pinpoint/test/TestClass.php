<?php

namespace pinpoint\test;

use pinpoint\test\Proxied_TestClass;
use pinpoint\test\traitTestPlugin;
use pinpoint\test\burden\depress\herb\e\e\f\longNp\victim;
use \over;
class TestClass extends Proxied_TestClass
{
    public function foo($a, $b, $v, $d) : array
    {
        $traitTestPlugin_foo_var = new traitTestPlugin(__METHOD__, $this, $a, $b, $v, $d);
        $traitTestPlugin_foo_ret = null;
        try {
            $traitTestPlugin_foo_var->onBefore();
            $traitTestPlugin_foo_ret = parent::foo($a, $b, $v, $d);
            $traitTestPlugin_foo_var->onEnd($traitTestPlugin_foo_ret);
            return $traitTestPlugin_foo_ret;
        } catch (\Exception $e) {
            $traitTestPlugin_foo_var->onException($e);
            throw $e;
        }
    }
    public function fooUseYield()
    {
        $traitTestPlugin_fooUseYield_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_fooUseYield_ret = null;
        try {
            $traitTestPlugin_fooUseYield_var->onBefore();
            $traitTestPlugin_fooUseYield_ret = parent::fooUseYield();
            $traitTestPlugin_fooUseYield_var->onEnd($traitTestPlugin_fooUseYield_ret);
            return $traitTestPlugin_fooUseYield_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function fooNoReturn()
    {
        $traitTestPlugin_fooNoReturn_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_fooNoReturn_ret = null;
        try {
            parent::fooNoReturn();
        } catch (\Exception $e) {
            $traitTestPlugin_fooNoReturn_var->onException($e);
            throw $e;
        }
    }
    public function fooNoReturnButReturn()
    {
        $victim_fooNoReturnButReturn_var = new victim(__METHOD__, $this);
        $victim_fooNoReturnButReturn_ret = null;
        try {
            $victim_fooNoReturnButReturn_ret = parent::fooNoReturnButReturn();
            return $victim_fooNoReturnButReturn_ret;
        } catch (\Exception $e) {
            $victim_fooNoReturnButReturn_var->onException($e);
            throw $e;
        }
    }
    public final function fooNaughtyFinal($a, $b, $c)
    {
        $over_fooNaughtyFinal_var = new over(__METHOD__, $this, $a, $b, $c);
        $over_fooNaughtyFinal_ret = null;
        try {
            $over_fooNaughtyFinal_var->onBefore();
            $over_fooNaughtyFinal_ret = parent::fooNaughtyFinal($a, $b, $c);
            $over_fooNaughtyFinal_var->onEnd($over_fooNaughtyFinal_ret);
            return $over_fooNaughtyFinal_ret;
        } catch (\Exception $e) {
            $over_fooNaughtyFinal_var->onException($e);
            throw $e;
        }
    }
}