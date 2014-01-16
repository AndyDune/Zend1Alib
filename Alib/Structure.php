<?php

namespace Alib;
class Structure
{
    protected $_root      = null;
    protected $_subdomain = 'www';
    protected $_fileNameConfig = 'config';
    protected $_fileNameRoutes = 'routes';

    /**
     * @var \Zend_Controller_Router_Interface
     */
    protected $_router      = null;

    /**
     * @var \Alib\Request
     */
    protected $_request      = null;

    protected $_levelFolders = array();
    protected $_levelObjects = array();

    protected $_countLevelFolders = 0;

    protected $_currentRoutesObject = null;

    /**
     * Первая конмада, выходящая за пределы структуры приложения.
     * Кроме обозначения
     * Пример:
     * URL: /news/politic
     * Структура /news/
     *
     * Папки politic не существует. Она хранится в этой переменной.
     *
     * @var string
     */
    protected $_firstCommandOutStructure = '';


    /**
     * @var Structure
     */
    static protected $instance = null;
    
    /**
     * 
     *
     * @return Structure
     */
    static function getInstance()
    {
        if (static::$instance == null)
        {
            static::$instance = new static();
            
        }
        return static::$instance;
    }    
    
    protected function __construct()
    {

    }

    /**
     * Количество существющих комманд.
     *
     * @return int
     */
    public function countLevelFolders()
    {
        return $this->_countLevelFolders;
    }


    /**
     * Возврат массива существующих комманд.
     * В данном контексте команда - это папка.
     *
     * @return array
     */
    public function getLevelFolders()
    {
        return $this->_levelFolders;
    }

    /**
     * Возврат массива объектов-уровней.
     * Уровень - это урл, у которого сществует реальный путь в структуре.
     *
     * @return array
     */
    public function getLevelObjects()
    {
        return $this->_levelObjects;
    }


    public function getReference($module = 'www', $key = null)
    {

        $file = $this->_root . '/' . strtolower($module) . '/' . 'reference.php';
        if (is_file($file))
        {
            include($file);
            if (!isset($application))
                return null;
            if (!$key)
                return $application;
            if (array_key_exists($key, $application))
                return $application[$key];
        }
        return null;

    }



    /**
     *  Установка текущегосубдомен.
     *
     * @param $value
     * @return Structure
     */
    public function setSubdomain($value)
    {
        $this->_subdomain = $value;
        return $this;
    }

    public function setRouter($router)
    {
        $this->_router = $router;
        return $this;
    }

    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * @param Request $request
     * @return Structure
     */
    public function setRequest(Request $request)
    {
        $this->_request = $request;
        return $this;
    }


    public function setRoot($path)
    {
        $this->_root = $path;
        return $this;
    }

    public function setFileNameRoutes($name)
    {
        $this->_fileNameRoutes = $name;
        return $this;
    }

    public function getFileNameRoutes()
    {
        return $this->_fileNameRoutes;
    }


    public function setFileNameComfig($name)
    {
        $this->_fileNameConfig = $name;
        return $this;
    }

    public function process()
    {
        $this->_preCheck();
        $this->_subdomainRoot = $this->_root . '/' . $this->_subdomain . '/';
        $this->_processSubdomainRoot();
        $this->_processLevels();
    }

    /**
     * @param $fileName
     * @param null $key
     * @return array|null|
     */
    public function getConfigArray($fileName, $key = null)
    {
        $file = $this->_root . '/' . $this->_subdomain . '/' . $fileName . '.php';
        if (is_file($file))
        {
            include($file);
            if (!isset($application))
                return null;
            if (!$key)
                return $application;
            if (array_key_exists($key, $application))
                return $application[$key];
        }
        return null;
    }


    protected function _preCheck()
    {

    }

    protected function _processLevels()
    {
        $commands = $this->_request->getRequestPathArray();
        $this->_levelFolders = array();

        $structure = $this->_root . '/' .$this->_subdomain . '/';

        $default_route = false;
        $current_separator = ''; // Пока нет промежуточных комманд
        foreach($commands as $key => $comm)
        {
            $current_folder = $structure . implode('/', $this->_levelFolders) . $current_separator . $comm;
            if (is_dir($current_folder))
            {
                $current_separator = '/';
                $this->_levelFolders[$key] = $comm;

                $this->_levelObjects[$key] = new Structure\Level($current_folder, $this->_levelFolders, $this);

                // это было для тестов
               // $this->_levelObjects[$key]->processedRouter();
                //
                //$config = $this->_levelObjects[$key]->getConfigObject($this->_fileNameConfig);

            }
            else
            {
                $this->_firstCommandOutStructure = $comm;
                break;
            }
        }
        $this->_countLevelFolders = count($this->_levelFolders);
        $this->_levelObjects[$this->_countLevelFolders]->processedRouter();
    }

    protected function _processSubdomainRoot()
    {
        $this->_levelObjects[0] = new Structure\Level($this->_subdomainRoot, array(), $this);
        $file_common_for_module = $this->_subdomainRoot . $this->_fileNameRoutes;

        if (is_file($file_common_for_module))
        {
            //$config = new \Zend_Config_Ini($file_common_for_module, 'base');
            //$this->_router->addConfig($config, 'routes');
        }
        return true;
    }
}