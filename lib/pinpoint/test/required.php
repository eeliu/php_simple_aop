<?php

namespace app\Foo;

use pinpiont\commPlugins;
use pinpiont\testPlugins;
use pinpiont\curlPlugins;
use pinpiont\curlSetoptPlugins;
class PDO extends \PDO
{
    function query(&...$args)
    {
        $pinpiont_commPlugins_query_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $pinpiont_commPlugins_query_var->onBefore();
            $pinpiont_commPlugins_query_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $pinpiont_commPlugins_query_var->onEnd($pinpiont_commPlugins_query_ret);
            return $pinpiont_commPlugins_query_ret;
        } catch (\Exception $e) {
            $pinpiont_commPlugins_query_var->onException($e);
            throw $e;
        }
    }
    function exec(&...$args)
    {
        $pinpiont_testPlugins_exec_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $pinpiont_testPlugins_exec_var->onBefore();
            $pinpiont_testPlugins_exec_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $pinpiont_testPlugins_exec_var->onEnd($pinpiont_testPlugins_exec_ret);
            return $pinpiont_testPlugins_exec_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
class Exception extends \Exception
{
    function __toString(&...$args)
    {
        $pinpiont_testPlugins___toString_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $pinpiont_testPlugins___toString_var->onBefore();
            $pinpiont_testPlugins___toString_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $pinpiont_testPlugins___toString_var->onEnd($pinpiont_testPlugins___toString_ret);
            return $pinpiont_testPlugins___toString_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
function array_push(...$args)
{
    $pinpiont_commPlugins_array_push_var = new commPlugins('array_push', null, $args);
    try {
        $pinpiont_commPlugins_array_push_var->onBefore();
        $pinpiont_commPlugins_array_push_ret = call_user_func_array('array_push', $args);
        $pinpiont_commPlugins_array_push_var->onEnd($pinpiont_commPlugins_array_push_ret);
        return $pinpiont_commPlugins_array_push_ret;
    } catch (\Exception $e) {
        $pinpiont_commPlugins_array_push_var->onException($e);
        throw $e;
    }
}
function curl_init(...$args)
{
    $pinpiont_curlPlugins_curl_init_var = new curlPlugins('curl_init', null, $args);
    try {
        $pinpiont_curlPlugins_curl_init_var->onBefore();
        $pinpiont_curlPlugins_curl_init_ret = call_user_func_array('curl_init', $args);
        $pinpiont_curlPlugins_curl_init_var->onEnd($pinpiont_curlPlugins_curl_init_ret);
        return $pinpiont_curlPlugins_curl_init_ret;
    } catch (\Exception $e) {
        $pinpiont_curlPlugins_curl_init_var->onException($e);
        throw $e;
    }
}
function curl_setopt(...$args)
{
    $pinpiont_curlSetoptPlugins_curl_setopt_var = new curlSetoptPlugins('curl_setopt', null, $args);
    try {
        $pinpiont_curlSetoptPlugins_curl_setopt_var->onBefore();
        $pinpiont_curlSetoptPlugins_curl_setopt_ret = call_user_func_array('curl_setopt', $args);
        $pinpiont_curlSetoptPlugins_curl_setopt_var->onEnd($pinpiont_curlSetoptPlugins_curl_setopt_ret);
        return $pinpiont_curlSetoptPlugins_curl_setopt_ret;
    } catch (\Exception $e) {
        $pinpiont_curlSetoptPlugins_curl_setopt_var->onException($e);
        throw $e;
    }
}