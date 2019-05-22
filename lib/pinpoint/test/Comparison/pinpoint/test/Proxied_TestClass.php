<?php


namespace pinpoint\test;


class Proxied_TestClass
{
    public function foo($a,$b,$v,$d) :array
    {
        return [$a,$b,$v,$d];
    }

    public function fooUseYield()
    {
        $i = 1000;
        yield $i +1;
        yield $i +2;
        yield $i +3;
    }

    public function fooNoReturn()
    {
        $i = 1000;
        throw new \Exception("I just want to throw sth");
    }

    public function fooNoReturnButReturn()
    {
        $i = 1000;
        throw new \Exception("I just want to throw sth");
        return "hello black hole";
    }

    public function fooNaughtyFinal($a,$b,$c)
    {
        yield $a;
        yield $b;
        yield $c;
    }

    public function fooTestBi()
    {
        $ch = \curl_init();
        curl_exec($ch);
        curl_close();
        $username = '2343';
        $passwd = "152351";
        $mysql = new PDO("mysql:host=localhost;dbname=user", $username , $passwd);
        $mysql->query('SELECT name, color, calories FROM fruit ORDER BY name');

    }

    protected function fooTestACPrivate()
    {
        echo "I'm a private function";
        return "OK";
    }
}require AOP_CACHE_DIR . 'pinpoint/test/TestClass_required.php';
