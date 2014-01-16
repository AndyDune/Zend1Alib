<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 06.11.12
 * Time: 9:53
 *
 * Превентивная загрузка классов из карты классов.
 *
 */
namespace Alib\Loader;
use Alib\Registry;
class ClassMap
{
    protected $_currentModule = '';
    protected $_modulesRoot = '';
    protected $_root = '';
    public function __construct($array = null)
    {
        $reg = Registry::getInstance();
        $this->_root = $reg->get('root');
        $this->_modulesRoot = $reg->get('root_modules');
        $this->_currentModule = $reg->get('module');
    }


    public function load($array = null)
    {
        $pathAlib = $this->_root . '/library/';
        if ($array)
        {
            foreach($array as $key => $value)
            {
                switch ($key)
                {
                    case 'Zend':
                        continue;
                    break;
                    case 'Alib':
                        $path = $pathAlib;
                    break;
                    case 'Application':
                        if (!isset($value['module']))
                        {
                            if (!is_array($value))
                            {
                                $value = ['path' => $value];
                            }
                            $value['module'] = $this->_currentModule;
                        }
                        else
                            $value['module'] = ucfirst($value['module']);
                        continue;
                    break;
                    default:
                        continue;
                }
                foreach($value as $className => $classPath)
                {
                    if (!interface_exists($className) and !trait_exists($className) and !class_exists($className))
                    {
                        require ($path . $classPath);
                    }
                }
            }
        }
        return $this;
    }

    protected function _loadClass($name, $path)
    {

    }

}
