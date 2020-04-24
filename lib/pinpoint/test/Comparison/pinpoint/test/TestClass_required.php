<?php

namespace pinpoint\test;

use pinpoint\test\traitTestPlugin;
class PDO extends \PDO
{
    public function query()
    {
        $traitTestPlugin_query_var = new traitTestPlugin(__METHOD__, $this, array());
        try {
            $traitTestPlugin_query_var->onBefore();
            $traitTestPlugin_query_ret = call_user_func_array(array('parent', __FUNCTION__), array());
            $traitTestPlugin_query_var->onEnd($traitTestPlugin_query_ret);
            return $traitTestPlugin_query_ret;
        } catch (\Exception $e) {
            $traitTestPlugin_query_var->onException($e);
            throw $e;
        }
    }
}
function curl_exec($ch)
{
    $traitTestPlugin_curl_exec_var = new traitTestPlugin('curl_exec', null, array($ch));
    try {
        $traitTestPlugin_curl_exec_var->onBefore();
        $traitTestPlugin_curl_exec_ret = call_user_func_array('curl_exec', array($ch));
        $traitTestPlugin_curl_exec_var->onEnd($traitTestPlugin_curl_exec_ret);
        return $traitTestPlugin_curl_exec_ret;
    } catch (\Exception $e) {
        $traitTestPlugin_curl_exec_var->onException($e);
        throw $e;
    }
}