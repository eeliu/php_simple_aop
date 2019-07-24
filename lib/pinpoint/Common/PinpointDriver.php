<?php
/**
 * User: eeliu
 * Date: 2/2/19
 * Time: 5:14 PM
 */

namespace pinpoint\Common;
use pinpoint\Common\OrgClassParse;
use pinpoint\Common\AopClassLoader;
use pinpoint\Common\ClassMap;
use pinpoint\Common\PluginParser;

class PinpointDriver
{
    protected static $instance;
    protected $clAr;
    protected $classMap;

    /**
     * @return mixed
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    public static function getInstance(){

        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->clAr = [];
    }

    public function init()
    {
        /// checking the cached file exist, if exist load it
//        if(file_exists(AOP_CACHE_DIR.'/__class_index_table' ))
//        {
//            $this->classMap =  new ClassMap(AOP_CACHE_DIR.'/__class_index_table');
//            AopClassLoader::init($this->classMap->classMap);
//            return ;
//        }

        /// scan user plugins which suffix is Plugin.php
        $this->classMap =  new ClassMap();
        $pluFiles = glob(PLUGINS_DIR."/*Plugin.php");
        $pluParsers = [];
        foreach ($pluFiles as $file)
        {
            $pluParsers[] = new PluginParser($file,$this->clAr);
        }


        foreach ($this->clAr as $cl=> $info)
        {
            $fullPath = Util::findFile($cl);
            if(!$fullPath)
            {
                continue;
            }

            $osr = new OrgClassParse($fullPath,$cl,$info);
            foreach ($osr->classIndex as $clName=>$path)
            {
                $this->classMap->insertMapping($clName,$path);
            }
        }

        $this->classMap->persistenceClassMapping(AOP_CACHE_DIR.'/__class_index_table');

        AopClassLoader::init($this->classMap->classMap);

    }


}
