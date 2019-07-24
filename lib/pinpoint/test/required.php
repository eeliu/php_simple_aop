<?php

namespace app\Foo;

use pinpiont\commPlugins;
use pinpiont\testPlugins;
use pinpiont\curlPlugins;
use pinpiont\curlSetoptPlugins;
class PDO extends \PDO
{
    function query()
    {
        $args = \func_get_args();
        $commPlugins_query_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_query_var->onBefore();
            $commPlugins_query_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_query_var->onEnd($commPlugins_query_ret);
            return $commPlugins_query_ret;
        } catch (\Exception $e) {
            $commPlugins_query_var->onException($e);
            throw $e;
        }
    }
    function exec($query)
    {
        $args = \func_get_args();
        $testPlugins_exec_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_exec_var->onBefore();
            $testPlugins_exec_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_exec_var->onEnd($testPlugins_exec_ret);
            return $testPlugins_exec_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
class Exception extends \Exception
{
    function __toString()
    {
        $args = \func_get_args();
        $testPlugins___toString_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins___toString_var->onBefore();
            $testPlugins___toString_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins___toString_var->onEnd($testPlugins___toString_ret);
            return $testPlugins___toString_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
function array_push($stack, $vars)
{
    $args = \func_get_args();
    $commPlugins_array_push_var = new commPlugins('array_push', null, $args);
    try {
        $commPlugins_array_push_var->onBefore();
        $commPlugins_array_push_ret = call_user_func_array('array_push', $args);
        $commPlugins_array_push_var->onEnd($commPlugins_array_push_ret);
        return $commPlugins_array_push_ret;
    } catch (\Exception $e) {
        $commPlugins_array_push_var->onException($e);
        throw $e;
    }
}
function curl_init($url)
{
    $args = \func_get_args();
    $curlPlugins_curl_init_var = new curlPlugins('curl_init', null, $args);
    try {
        $curlPlugins_curl_init_var->onBefore();
        $curlPlugins_curl_init_ret = call_user_func_array('curl_init', $args);
        $curlPlugins_curl_init_var->onEnd($curlPlugins_curl_init_ret);
        return $curlPlugins_curl_init_ret;
    } catch (\Exception $e) {
        $curlPlugins_curl_init_var->onException($e);
        throw $e;
    }
}
function curl_setopt($ch, $option, $value)
{
    $args = \func_get_args();
    $curlSetoptPlugins_curl_setopt_var = new curlSetoptPlugins('curl_setopt', null, $args);
    try {
        $curlSetoptPlugins_curl_setopt_var->onBefore();
        $curlSetoptPlugins_curl_setopt_ret = call_user_func_array('curl_setopt', $args);
        $curlSetoptPlugins_curl_setopt_var->onEnd($curlSetoptPlugins_curl_setopt_ret);
        return $curlSetoptPlugins_curl_setopt_ret;
    } catch (\Exception $e) {
        $curlSetoptPlugins_curl_setopt_var->onException($e);
        throw $e;
    }
}