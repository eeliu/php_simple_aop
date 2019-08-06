<?php
/**
 * Copyright 2019 NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * Focus on Built-in
 */
namespace pinpoint\Common;

use PhpParser\BuilderFactory;
use PhpParser\NodeAbstractTest;
use PhpParser\PrettyPrinter;
use PhpParser\Node;

class GenRequiredBIFile
{
    public $namespace;
    public $clName;


    /// class PDO extends \PDO{}
    protected $classNode;

    // function curl_init() {}
    // function curl_setopt() {}
    protected $funcNodes;

    protected $useArray;
    protected  $fileNode;
    private $factory;


    public function __construct($np)
    {
        $this->namespace      = $np;
        $this->factory        = new BuilderFactory;
        $this->useArray       = [];
        $this->fileNode       = [];
        $this->classNode      = [];
        $this->funcNodes      = [];
    }

    private function creatFuncParamArgs($funcName)
    {
        $refFunc = new \ReflectionFunction($funcName);
        $argsNode = [];
        foreach ($refFunc->getParameters() as $param)
        {
            $pNode = $this->makeParam($param);

            if($param->isPassedByReference())
                $pNode->makeByRef();

            $argsNode[] = $pNode;
        }
        return $argsNode;
    }

    private function makeArrayParam($param)
    {
        $node = $this->factory->param($param->getName())->setType('array');

        if ($param->isVariadic())
            $node->makeVariadic();
        elseif ($param->isOptional())
            $node->setDefault(new Node\Expr\ConstFetch(new Node\Name('null')));

        if($param->isPassedByReference())
            $node->makeByRef();

        return $node;
    }

    private function makeOtherParam($param)
    {
        $node =  $this->factory->param($param->getName());

        if ($param->isVariadic())
            $node->makeVariadic();
        elseif($param->isOptional())
            $node->setDefault(new Node\Expr\ConstFetch(new Node\Name('null')));

        if($param->isPassedByReference())
            $node->makeByRef();

        return $node;
    }

    private function makeParam($param)
    {
        if($param->isArray()){
            return $this->makeArrayParam($param);
        }else{
            return $this->makeOtherParam($param);
        }
    }

    private function creatMethodParamArgs($className,$funcName)
    {
        $refFunc = new \ReflectionMethod($className,$funcName);
        $argsNode = [];
        foreach ($refFunc->getParameters() as $param)
        {
            $pNode =  $this->makeParam($param);

            $argsNode[] = $pNode;
        }

        return $argsNode;
    }

    public function extendsFunc($funcName,$info)
    {

        list($mode, $namespace, $className) = $info;
        $np = $namespace . '\\' . $className;

        if(!in_array($np,$this->useArray)){
            $this->useArray[] = $np;
        }

        $funcVar = new Node\Arg(new Node\Scalar\String_($funcName));

        $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));

        // funcName($statement1,$statement2)

        $thisFunc =  $this->factory->function($funcName)->addParams( $this->creatFuncParamArgs($funcName));

        //$args = \func_get_args();
        $getArgsStm = new Node\Stmt\Expression(
            new Node\Expr\Assign(
                new Node\Expr\Variable("args"),
                new Node\Expr\FuncCall(
                    new Node\Name\FullyQualified('func_get_args'),
                    [

                    ]
                )
            )
        );
        $thisFunc->addStmt($getArgsStm);

        // $var = new commPlugins(__METHOD__,this,$args)
        $varName = $className.'_'.$funcName.'_var';
        $retName = $className.'_'.$funcName.'_ret';
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($varName),
            $this->factory->new($className,[$funcVar,$selfVar,new Node\Expr\Variable("args")])));
        $thisFunc->addStmt($newPluginsStm);

        $tryBlock = [];
        $catchNode = [];

        if ($mode & PluginParser::BEFORE)
        {
            // $var->onBefore();
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore"));
        }

        // $ret  = call_user_func_array("function",parater)
        $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\FuncCall(
                    new Node\Name("call_user_func_array"),
                    [
                        new Node\Arg(new Node\Scalar\String_($funcName)),
                        new Node\Expr\Variable("args"),
                    ]
                )
            )
        );

        /// $var->onEnd($ret);
        if($mode & PluginParser::END)
        {
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(
                    new Node\Expr\Variable($varName),
                    "onEnd",
                    [new Node\Expr\Variable($retName)]
                )
            );
        }

        /// return $var;
        $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable($retName));

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e')) ;

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable($varName),
                    "onException",$expArgs));

        }

        $catchBlock[] = new Node\Stmt\Throw_(new Node\Expr\Variable("e"));

        $catchNode[] = new Node\Stmt\Catch_([new Node\Name('\Exception')],
            new Node\Expr\Variable('e'),
            $catchBlock);

        $tryCatchFinallyNode = new Node\Stmt\TryCatch($tryBlock,$catchNode);
        //try {}catch{}
        $thisFunc->addStmt($tryCatchFinallyNode);

        $this->funcNodes[] = $thisFunc;
    }


    /// $mode,$np,$className
    public function extendsMethod($dstClass,$thisFuncName,$info)
    {

        list($mode, $namespace, $className) = $info;

        $np = $namespace . '\\' . $className;

        if(!in_array($np,$this->useArray)){
            $this->useArray[] = $np;
        }

        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());
        $selfVar = new Node\Arg(new Node\Expr\Variable('this'));

        /// funcName($a,$b,$c)
        $thisMethod = $this->factory->method($thisFuncName)->addParams(
            $this->creatMethodParamArgs($dstClass,$thisFuncName));

        $varName = $className.'_'.$thisFuncName.'_var';
        $retName = $className.'_'.$thisFuncName.'_ret';

        //$args = \func_get_args();
        $getArgsStm = new Node\Stmt\Expression(
            new Node\Expr\Assign(
                new Node\Expr\Variable("args"),
                new Node\Expr\FuncCall(
                    new Node\Name\FullyQualified('func_get_args'),
                    [

                    ]
                )
            )
        );
        $thisMethod->addStmt($getArgsStm);


        // $var = new commPlugins(__METHOD__,this,$args)
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($varName),
            $this->factory->new($className,[$funcVar,$selfVar,new Node\Expr\Variable("args")])));
        $thisMethod->addStmt($newPluginsStm);


        $tryBlock = [];
        $catchNode = [];

        // $var->onBefore();
        if ($mode & PluginParser::BEFORE)
        {
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore"));
        }

        // $ret  = call_user_func_array
        $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($retName),
            new Node\Expr\FuncCall(
                new Node\Name("call_user_func_array"),
                [
                    new Node\Arg(new Node\Expr\Array_(
                        [
                            new Node\Expr\ArrayItem(new Node\Scalar\String_('parent')),
                            new Node\Scalar\MagicConst\Function_()
                        ]
                    )),
                    new Node\Expr\Variable("args"),
                ]
                )
            )
        );

        /// $var->onEnd($ret);
        if($mode & PluginParser::END)
        {
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(
                    new Node\Expr\Variable($varName),
                    "onEnd",
                    [new Node\Expr\Variable($retName)]
                )
            );
        }

        /// return $var;
        $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable($retName));

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e')) ;

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable($varName),
                    "onException",$expArgs));

        }

        $catchBlock[] = new Node\Stmt\Throw_(new Node\Expr\Variable("e"));

        $catchNode[] = new Node\Stmt\Catch_([new Node\Name('\Exception')],
            new Node\Expr\Variable('e'),
            $catchBlock);

        $tryCatchFinallyNode = new Node\Stmt\TryCatch($tryBlock,$catchNode);

        $thisMethod->addStmt($tryCatchFinallyNode);

        if( !array_key_exists($dstClass, $this->classNode))
        {
            $this->classNode[$dstClass] = $this->factory->class($dstClass)->extend('\\'.$dstClass);
        }

        $this->classNode[$dstClass]->addStmt($thisMethod);
    }

    public function loadToFile($fullPath)
    {
        $useNodes = [];
        foreach ($this->useArray as $useAlias){
            $useNodes[] = $this->factory->use($useAlias);
        }

        $this->fileNode = $this->factory->namespace($this->namespace)
            ->addStmts($useNodes)
            ->addStmts(
                $this->classNode
            )->addStmts($this->funcNodes);

        $stmts = array($this->fileNode->getNode());
        $prettyPrinter = new PrettyPrinter\Standard();
        $context =  $prettyPrinter->prettyPrintFile($stmts);
        Util::flushStr2File($context,$fullPath);
    }

}
