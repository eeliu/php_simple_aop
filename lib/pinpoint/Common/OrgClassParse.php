<?php
namespace pinpoint\Common;

use pinpoint\Common\OriginClassFile;
use pinpoint\Common\ShadowClassFile;
use pinpoint\Common\CodeVisitor;

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\BuilderFactory;
use pinpoint\Common\Util;


class OrgClassParse
{
    private $originFile;
    private $lexer;
    private $parser;
    private $traverser;
    private $printer;

    public $cfg;

    private $originClassNode;

    private $rawOrigStmts;

    public $classIndex = [];

    public $className;// app\foo\DBManager

    const PRE_FIX = 'Proxied_';

    public $mFuncAr;

    public $originClass;
    public $shadowClass;

    public $orgClassPath;
    public $shadowClassPath;


    public function __construct($fullPath, $cl, $info, &$cfg)
    {
        assert(file_exists($fullPath));

        $this->cfg = &$cfg;
        $this->className = $cl;
        $this->mFuncAr = $info;
        $this->originFile = $fullPath;


        $this->lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine',
                'endLine',
                'startTokenPos',
                'endTokenPos',
            ],
        ]);

        $this->parser = new Parser\Php7($this->lexer, [
            'useIdentifierNodes' => true,
            'useConsistentVariableNodes' => true,
            'useExpressionStatements' => true,
            'useNopStatements' => false,
        ]);

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NodeVisitor\CloningVisitor());
        $this->traverser->addVisitor(new CodeVisitor($this));

        $this->printer = new PrettyPrinter\Standard();

        $this->originClass = new OriginClassFile($fullPath,OrgClassParse::PRE_FIX);
        $this->shadowClass =  new ShadowClassFile(OrgClassParse::PRE_FIX);

        $this->parseOriginFile();
    }

    protected function parseOriginFile()
    {
        $code = file_get_contents($this->originFile);

        $this->rawOrigStmts = $this->parser->parse($code);

        $this->originClassNode = $this->traverser->traverse($this->rawOrigStmts);

    }

    /// convert $node to file
    public function orgClassNodeDoneCB($node,$fullName)
    {
        $fullPath = $this->cfg['cache_dir'].'/'.str_replace('\\','/',$fullName).'.php';
        // try to keep blank and filenu
        $orgClassContext = $this->printer->printFormatPreserving(
            $node,
            $this->rawOrigStmts,
            $this->lexer->getTokens());

        Util::flushStr2File($orgClassContext,$fullPath);
        $this->classIndex[$fullName] = $fullPath;
    }

    /// convert $node to file
    public function shadowClassNodeDoneCB(&$node,$fullName)
    {

        $fullPath = $this->cfg['cache_dir'].'/'.str_replace('\\','/',$fullName).'.php';
        $context= $this->printer->prettyPrintFile(array($node));
        Util::flushStr2File($context,$fullPath);
        $this->classIndex[$fullName] = $fullPath;

    }


    public function generateAllClass():array
    {
        /// ast to source
        return $this->classIndex;
    }
}