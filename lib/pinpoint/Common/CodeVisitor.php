<?php
/**
 * User: eeliu
 * Date: 2/13/19
 * Time: 10:33 AM
 */

namespace pinpoint\Common;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use pinpoint\Common\GenRequiredBIFile;

class CodeVisitor extends NodeVisitorAbstract
{

    protected $ospIns;
    private $curNamespace;
    private $curClass;

    protected $builtInAr = []; // curl_init PDO

    public function __construct($ospIns)
    {
        assert($ospIns instanceof OrgClassParse);
        $this->ospIns = $ospIns;
        $this->curClass = $ospIns->className;
    }

    public function enterNode(Node $node)
    {
        if($node instanceof Node\Stmt\Namespace_)
        {
            $this->curNamespace = $node->name->toString();
            /// set namespace
            $this->ospIns->shadowClass->handleEnterNamespaceNode($node);
            $this->ospIns->originClass->handleEnterNamespaceNode($node);

            $reqFile = new GenRequiredBIFile($this->curNamespace);
            foreach ($this->ospIns->mFuncAr as $key => $value)
            {
                $ret = Util::isBuiltIn($key);

                if($ret == Util::Method){
                    list($clName,$clMethod) = preg_split ("/[::|\\\]/",$key,-1,PREG_SPLIT_NO_EMPTY);
                    $this->builtInAr[] = $clName;
                    $reqFile->extendsMethod($clName,$clMethod,$value);
                }elseif ($ret == Util::Function){

                    list($funcName) = preg_split ("/[\\\]/",$key,-1,PREG_SPLIT_NO_EMPTY);
                    $this->builtInAr [] = $funcName ;
                    $reqFile->extendsFunc($funcName,$value);
                }else{
                    //do nothing
                }
            }

            $reqFileName = $this->ospIns->cfg['cache_dir'].'/'.str_replace('\\','/', $this->curClass).'_required.php';

            $reqFile->loadToFile($reqFileName);
            $this->ospIns->originClass->addRequiredFile($reqFileName);
        }
        elseif ($node instanceof Node\Stmt\Class_){

            if( $this->curNamespace.'\\'.$node->name->toString() != $this->curClass)
            {
                // ignore uncared
                echo "NodeTraverser::DONT_TRAVERSE_CHILDREN @".$this->curClass;
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

            $this->ospIns->shadowClass->handleEnterClassNode($node);
            $this->ospIns->originClass->handleEnterClassNode($node);

        }elseif ($node instanceof Node\Stmt\ClassMethod)
        {
            $this->ospIns->shadowClass->handleClassEnterMethodNode($node);
            $this->ospIns->originClass->handleClassEnterMethodNode($node);
        }
        elseif ( $node instanceof Node\Stmt\Return_)
        {
            $this->ospIns->shadowClass->markHasReturn($node);
        }
        elseif ($node instanceof Node\Expr\Yield_){
            $this->ospIns->shadowClass->markHasReturn($node);
        }
    }


    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod){
            $func = trim( $node->name->toString());
            if(array_key_exists($func,$this->ospIns->mFuncAr))
            {
                $this->ospIns->shadowClass->handleClassLeaveMethodNode($node,$this->ospIns->mFuncAr[$func]);
                $this->ospIns->originClass->handleClassLeaveMethodNode($node,$this->ospIns->mFuncAr[$func]);
                /// remove the func
                unset( $this->ospIns->mFuncAr[$func] );
            }
        }elseif ($node instanceof Node\Name\FullyQualified){
            // use Foo\Name replace \Name
            $name = $node->toString();
            if(! in_array($name,$this->builtInAr) ){
                return ;
            }
            return $this->ospIns->originClass->handleFullyQualifiedNode($node);
        }
        elseif ($node instanceof Node\Scalar\MagicConst){

            $this->ospIns->originClass->handleMagicConstNode($node);
        }elseif ($node instanceof Node\Stmt\Namespace_){
            return $this->ospIns->originClass->handleLeaveNamespace($node);
        }
        elseif ($node instanceof Node\Stmt\Class_){
            $this->ospIns->originClass->handleLeaveClassNode($node);

        }elseif ($node instanceof Node\Stmt\UseUse){
            /// scene : use \PDO
            ///        replace \PDO to \Np\PDO
            if( in_array($node->name->toString(),$this->builtInAr))
            {
                $node->name   = new Node\Name($this->curNamespace.'\\'.$node->name->toString());
            }
            return $node;
        }
    }

    public function afterTraverse(array $nodes)
    {
        $node = $this->ospIns->originClass->handleAfterTravers($nodes,
            $this->ospIns->mFuncAr);

        $this->ospIns->orgClassNodeDoneCB($node,$this->ospIns->originClass->className);

        $node = $this->ospIns->shadowClass->handleAfterTravers($nodes,
            $this->ospIns->mFuncAr);

        $this->ospIns->shadowClassNodeDoneCB($node,$this->ospIns->shadowClass->className);

    }

}
