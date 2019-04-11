<?php
/**
 * User: eeliu
 * Date: 2/14/19
 * Time: 11:36 AM
 */

namespace pinpoint\Common;

use PhpParser\Node;

/**
 * Class ClassFile
 *
 * A abstract php-parse node
 *      namespace node
 *      use nodes
 *      class node
 *      required node
 *
 * @package pinpoint\Common
 */
abstract class ClassFile
{
    public $appendingFile = array();

    public $node;

    protected $prefix;

    public $namespace;

    public $className; /// Foo\A Foo\B

    public $classMethod;

    public $funcName; // only for __FUNCTION__

    protected $dir;

    public $hasRet;

    public $fileNode;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function handleEnterNamespaceNode(&$node)
    {
        assert($node instanceof Node\Stmt\Namespace_);
        $this->namespace = trim($node->name->toString());
    }

    public function handleEnterClassNode(&$node)
    {
        assert($node instanceof Node\Stmt\Class_);
        $this->className = trim($this->namespace.'\\'.$node->name->toString());
    }


    public function handleClassEnterMethodNode(&$node)
    {
        assert($node instanceof Node\Stmt\ClassMethod);
        $this->funcName = $node->name->toString();
        $this->classMethod =$this->className.'::'.$this->funcName;
        $this->hasRet = false;
    }

    abstract function handleClassLeaveMethodNode(&$node,&$info);

    public function markHasReturn(&$node)
    {
        $this->hasRet = true;
    }

    abstract function handleAfterTravers(&$nodes,&$mFuncAr);
    abstract function handleLeaveNamespace(&$nodes);
}
