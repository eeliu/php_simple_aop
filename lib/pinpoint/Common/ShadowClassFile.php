<?php
/**
 * User: eeliu
 * Date: 2/13/19
 * Time: 4:41 PM
 */

namespace pinpoint\Common;

use pinpoint\Common\ClassFile;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;
use pinpoint\Common\PluginParser;

class ShadowClassFile extends ClassFile
{
    private $factory;

    private $classNode; // class { }
    private $traitNode;

    private $extendTraitName;

    private $useArray = [];

    private $handleMethodNodeLeaveFunc = '';
    private $handleEndTraversFunc='';

    public function __construct($prefix = 'Proxied_')
    {
        parent::__construct($prefix);
        $this->factory= new BuilderFactory();

        $this->classNode = null;
        $this->traitNode = null;
    }


    public function handleEnterNamespaceNode(&$node)
    {
        parent::handleEnterNamespaceNode($node);

    }

    public function handleEnterClassNode(&$node)
    {
        assert($node instanceof Node\Stmt\Class_);
        parent::handleEnterClassNode($node);

        $extendClass = $this->prefix.$node->name->toString();
        $this->classNode  = $this->factory->class(trim($node->name->toString()))->extend($extendClass);
        $this->useArray[] = $this->namespace.'\\'.$extendClass;

        switch($node->flags) {
            case Node\Stmt\Class_::MODIFIER_FINAL:
                $this->classNode->makeFinal();
                break;
            case Node\Stmt\Class_::MODIFIER_ABSTRACT:
                $this->classNode->makeAbstract();
                break;
           default:
                break;
        }

        $this->handleMethodNodeLeaveFunc = 'handleClassLeaveMethodNode';
        $this->handleEndTraversFunc      = 'handleAfterTraversClass';
    }

    public function handleEnterTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        parent::handleEnterTraitNode($node);
        $this->traitNode  = $this->factory->trait(trim($node->name->toString()));
        $this->extendTraitName = $this->prefix.$node->name->toString();
        $this->handleMethodNodeLeaveFunc = 'handleTraitLeaveMethodNode';
        $this->handleEndTraversFunc   = 'handleAfterTraversTrait';
    }

    public static function convertParamsName2Arg($params)
    {
        assert(is_array($params));

        $args = [];

        foreach ($params as $param)
        {
            assert($param instanceof Node\Param);
            $args [] = new Node\Arg($param->var);
        }

        return  $args;
    }


    public function handleClassLeaveMethodNode(&$node,&$info)
    {
        /// todo this func looks ugly

        assert($node instanceof Node\Stmt\ClassMethod);

        list($mode, $namespace, $className) = $info;

        $np = $namespace . '\\' . $className;

        if(!in_array($np,$this->useArray)){
            $this->useArray[] = $np;
        }

        // foo_1
        $thisFuncName = $node->name->toString();

        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        // public function funcName(){}
        $thisMethod = $this->factory->method($thisFuncName);

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $thisMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            $thisMethod->makePrivate();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {

            $thisMethod->makeAbstract();
        }

        if($node->flags & Node\Stmt\Class_::MODIFIER_FINAL){
            $thisMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $thisMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
        }else{
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }

        $pluginArgs  = array_merge([$funcVar,$selfVar],ShadowClassFile::convertParamsName2Arg($node->params));

        $thisMethod->addParams($node->params);
        if($node->returnType){
            $thisMethod->setReturnType($node->returnType);
        }

        $varName = $className.'_'.$thisFuncName.'_var';
        $retName = $className.'_'.$thisFuncName.'_ret';

        /// $var = new CommonPlugins(__FUNCTION__,self,$p);
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($varName),
            $this->factory->new($className, $pluginArgs)));

        $thisMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null'))));
        $thisMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        if ($mode & PluginParser::BEFORE)
        {
            // $var->onBefore();
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore"));
        }

        if ($this->hasRet) {
            /// $ret = paraent::$thisFuncName();
            $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\StaticCall(new Node\Name("parent"),
                    new Node\Identifier($thisFuncName),
                    ShadowClassFile::convertParamsName2Arg($node->params))));

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

        } else {
            /// paraent::$thisFuncName();

            $tryBlock[] = new Node\Stmt\Expression($this->factory->staticCall(
                new Node\Name("parent")
                , new Node\Identifier($thisFuncName),
                ShadowClassFile::convertParamsName2Arg($node->params)));

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
        }

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

        $this->classNode->addStmt($thisMethod);
    }

    public function handleTraitLeaveMethodNode(&$node,&$info)
    {
        /// todo this func looks ugly

        /// - check use , add  use Proxied_Foo { }
        /// - insert alias use Proxied_Foo::xxx as Foo_xxxx
        /// - new function xxxx

        assert($node instanceof Node\Stmt\ClassMethod);

        list($mode, $namespace, $className) = $info;

        // foo_1
        $thisFuncName = $node->name->toString();

        // $this->extendTraitName::$thisFuncName as $this->extendTraitName_$thisFuncName;
        $this->useArray[] = $thisFuncName;
        $extendMethodName = $this->extendTraitName.'_'.$thisFuncName;


        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        // public function funcName(){}
        $thisMethod = $this->factory->method($thisFuncName);

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $thisMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            $thisMethod->makePrivate();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {

            $thisMethod->makeAbstract();
        }

        if($node->flags & Node\Stmt\Class_::MODIFIER_FINAL){
            $thisMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $thisMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
        }else{
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }

        $pluginArgs  = array_merge([$funcVar,$selfVar],ShadowClassFile::convertParamsName2Arg($node->params));

        $thisMethod->addParams($node->params);
        if($node->returnType){
            $thisMethod->setReturnType($node->returnType);
        }

        $varName = $className.'_'.$thisFuncName.'_var';
        $retName = $className.'_'.$thisFuncName.'_ret';

        /// $var = new CommonPlugins(__FUNCTION__,self,$p);
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($varName),
            $this->factory->new($className, $pluginArgs)));

        $thisMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Stmt\Expression(new Node\Expr\Assign(new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null'))));
        $thisMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        if ($mode & PluginParser::BEFORE)
        {
            // $var->onBefore();
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore"));
        }

        if ($this->hasRet) {
            /// $ret = $this->Proxied_xxx(&...$args);
            $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\MethodCall(new Node\Expr\Variable("this"),
                    new Node\Identifier($extendMethodName),
                    ShadowClassFile::convertParamsName2Arg($node->params))));

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

        } else {
            /// $this->>$thisFuncName();
            $tryBlock[] = new Node\Stmt\Expression( new Node\Expr\MethodCall(new Node\Expr\Variable("this"),
                    new Node\Identifier($extendMethodName),
                ShadowClassFile::convertParamsName2Arg($node->params)));

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
        }

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

        $this->traitNode->addStmt($thisMethod);

    }

    public function handleLeaveMethodNode(&$node,&$info)
    {
        call_user_func_array([$this,$this->handleMethodNodeLeaveFunc],[&$node,&$info]);
    }

    public function handleAfterTraversClass(&$nodes,&$mFuncAr)
    {
        $useNodes = [];
        foreach ($this->useArray as $useAlias){
            $useNodes[] = $this->factory->use($useAlias);
        }

        $this->fileNode = $this->factory->namespace($this->namespace);

        if(count($useNodes) > 0){

            $this->fileNode->addStmts($useNodes);
        }

        $this->fileNode->addStmt($this->classNode);

        $this->fileName = $this->className;

        return $this->fileNode->getNode();
    }

    public function handleAfterTraversTrait(&$nodes,&$mFuncAr)
    {
        // use Proxied_Foo{}
        $useTraitNode =$this->factory->useTrait($this->extendTraitName);

        foreach ($this->useArray as $alias)
        {
            // $extendMethodName::thisfuncName as $this->extendTraitName.'_'.$thisFuncName;
            $useTraitNode->with($this->factory->traitUseAdaptation($this->extendTraitName,$alias)->as($this->extendTraitName.'_'.$alias));
        }

        $this->traitNode->addStmt($useTraitNode);

        $this->fileNode = $this->factory->namespace($this->namespace)
            ->addStmt($this->traitNode);

        $this->fileName = $this->traitName;

        return $this->fileNode->getNode();
    }

    public function handleAfterTravers(&$nodes,&$mFuncAr)
    {
        return call_user_func_array([$this,$this->handleEndTraversFunc],[&$nodes,&$mFuncAr]);
    }

    function handleLeaveNamespace(&$nodes)
    {
        // do nothing
    }
}
