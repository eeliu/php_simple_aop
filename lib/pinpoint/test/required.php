<?php

namespace app\Foo;

use pinpoint\commPlugins;
use pinpoint\testPlugins;
use pinpoint\curlPlugins;
use pinpoint\curlSetoptPlugins;
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
    function prepare($statement, $options = null)
    {
        $args = \func_get_args();
        $commPlugins_prepare_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_prepare_var->onBefore();
            $commPlugins_prepare_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_prepare_var->onEnd($commPlugins_prepare_ret);
            return $commPlugins_prepare_ret;
        } catch (\Exception $e) {
            $commPlugins_prepare_var->onException($e);
            throw $e;
        }
    }
    function rollBack()
    {
        $args = \func_get_args();
        $commPlugins_rollBack_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_rollBack_var->onBefore();
            $commPlugins_rollBack_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_rollBack_var->onEnd($commPlugins_rollBack_ret);
            return $commPlugins_rollBack_ret;
        } catch (\Exception $e) {
            $commPlugins_rollBack_var->onException($e);
            throw $e;
        }
    }
    function lastInsertId($seqname = null)
    {
        $args = \func_get_args();
        $commPlugins_lastInsertId_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_lastInsertId_var->onBefore();
            $commPlugins_lastInsertId_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_lastInsertId_var->onEnd($commPlugins_lastInsertId_ret);
            return $commPlugins_lastInsertId_ret;
        } catch (\Exception $e) {
            $commPlugins_lastInsertId_var->onException($e);
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
            $testPlugins_exec_var->onException($e);
            throw $e;
        }
    }
}
class PDOStatement extends \PDOStatement
{
    function fetchAll($how = null, $class_name = null, $ctor_args = null)
    {
        $args = \func_get_args();
        $testPlugins_fetchAll_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_fetchAll_var->onBefore();
            $testPlugins_fetchAll_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_fetchAll_var->onEnd($testPlugins_fetchAll_ret);
            return $testPlugins_fetchAll_ret;
        } catch (\Exception $e) {
            $testPlugins_fetchAll_var->onException($e);
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
class Redis extends \Redis
{
    function connect($host, $port = null, $timeout = null, $retry_interval = null)
    {
        $args = \func_get_args();
        $testPlugins_connect_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_connect_var->onBefore();
            $testPlugins_connect_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_connect_var->onEnd($testPlugins_connect_ret);
            return $testPlugins_connect_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    function hGet($key, $member)
    {
        $args = \func_get_args();
        $testPlugins_hGet_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_hGet_var->onBefore();
            $testPlugins_hGet_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_hGet_var->onEnd($testPlugins_hGet_ret);
            return $testPlugins_hGet_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    function ttl($key)
    {
        $args = \func_get_args();
        $testPlugins_ttl_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_ttl_var->onBefore();
            $testPlugins_ttl_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_ttl_var->onEnd($testPlugins_ttl_ret);
            return $testPlugins_ttl_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    function hDel($key, $member, ...$other_members)
    {
        $args = \func_get_args();
        $testPlugins_hDel_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_hDel_var->onBefore();
            $testPlugins_hDel_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_hDel_var->onEnd($testPlugins_hDel_ret);
            return $testPlugins_hDel_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    function del($key, ...$other_keys)
    {
        $args = \func_get_args();
        $testPlugins_del_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_del_var->onBefore();
            $testPlugins_del_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_del_var->onEnd($testPlugins_del_ret);
            return $testPlugins_del_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    function hGetAll($key)
    {
        $args = \func_get_args();
        $testPlugins_hGetAll_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_hGetAll_var->onBefore();
            $testPlugins_hGetAll_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_hGetAll_var->onEnd($testPlugins_hGetAll_ret);
            return $testPlugins_hGetAll_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
class mysqli_result extends \mysqli_result
{
    function fetch_all()
    {
        $args = \func_get_args();
        $testPlugins_fetch_all_var = new testPlugins(__METHOD__, $this, $args);
        try {
            $testPlugins_fetch_all_var->onBefore();
            $testPlugins_fetch_all_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $testPlugins_fetch_all_var->onEnd($testPlugins_fetch_all_ret);
            return $testPlugins_fetch_all_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
function array_push(&$stack, ...$vars)
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
function array_merge($arr1, ...$arrays)
{
    $args = \func_get_args();
    $commPlugins_array_merge_var = new commPlugins('array_merge', null, $args);
    try {
        $commPlugins_array_merge_var->onBefore();
        $commPlugins_array_merge_ret = call_user_func_array('array_merge', $args);
        $commPlugins_array_merge_var->onEnd($commPlugins_array_merge_ret);
        return $commPlugins_array_merge_ret;
    } catch (\Exception $e) {
        $commPlugins_array_merge_var->onException($e);
        throw $e;
    }
}
function curl_init($url = null)
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
function date($format, $timestamp = null)
{
    $args = \func_get_args();
    $curlSetoptPlugins_date_var = new curlSetoptPlugins('date', null, $args);
    try {
        $curlSetoptPlugins_date_var->onBefore();
        $curlSetoptPlugins_date_ret = call_user_func_array('date', $args);
        $curlSetoptPlugins_date_var->onEnd($curlSetoptPlugins_date_ret);
        return $curlSetoptPlugins_date_ret;
    } catch (\Exception $e) {
        $curlSetoptPlugins_date_var->onException($e);
        throw $e;
    }
}