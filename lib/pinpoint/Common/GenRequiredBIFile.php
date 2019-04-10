<?php
namespace pinpoint\Common;

use PhpParser\BuilderFactory;
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


    public function extendsFunc($funcName,$info)
    {
        $refFunc = new \ReflectionFunction($funcName);
        list($mode, $namespace, $className) = $info;
        $np = $namespace . '\\' . $className;

        if(!in_array($np,$this->useArray)){
            $this->useArray[] = $np;
        }

//        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Function_());

        $funcVar = new Node\Arg(new Node\Scalar\String_($funcName));

        $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));

        $thisFunc =  $this->factory->function($funcName)->addParam(
            $this->factory->param('args')->makeVariadic());

        // $var = new commPlugins(__METHOD__,this,$args)
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable("var"),
            $this->factory->new($className,[$funcVar,$selfVar,new Node\Expr\Variable("args")])));
        $thisFunc->addStmt($newPluginsStm);

        $tryBlock = [];
        $catchNode = [];

        if ($mode & PluginParser::BEFORE)
        {
            // $var->onBefore();
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable("var"), "onBefore"));
        }

        // $ret  = call_user_func_array("function",parater)
        $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                new Node\Expr\Variable("ret"),
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
                    new Node\Expr\Variable("var"),
                    "onEnd",
                    [new Node\Expr\Variable('ret')]
                )
            );
        }

        /// return $var;
        $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable('ret'));

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e')) ;

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable("var"),
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
        $parameters = new \ReflectionMethod($dstClass,$thisFuncName);

        if($parameters->isStatic()){
            throw new \Exception("not supported");
        }

        list($mode, $namespace, $className) = $info;

        $np = $namespace . '\\' . $className;

        if(!in_array($np,$this->useArray)){
            $this->useArray[] = $np;
        }

        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());
        $selfVar = new Node\Arg(new Node\Expr\Variable('this'));


        $thisMethod = $this->factory->method($thisFuncName)->addParam($this->factory->param('args')->makeByRef()->makeVariadic());

        // $var = new commPlugins(__METHOD__,this,$args)
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable("var"),
            $this->factory->new($className,[$funcVar,$selfVar,new Node\Expr\Variable("args")])));
        $thisMethod->addStmt($newPluginsStm);


        $tryBlock = [];
        $catchNode = [];

        // $var->onBefore();
        if ($mode & PluginParser::BEFORE)
        {
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable("var"), "onBefore"));
        }

        // $ret  = call_user_func_array
        $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable("ret"),
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
                    new Node\Expr\Variable("var"),
                    "onEnd",
                    [new Node\Expr\Variable('ret')]
                )
            );
        }

        /// return $var;
        $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable('ret'));

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e')) ;

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable("var"),
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
