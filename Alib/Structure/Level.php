<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 21.02.12
 * Time: 22:46
 */
namespace Alib\Structure;
class Level
{
    protected $_path     = '';

    protected $_configObjects = array();
    protected $_strings  = array();

    protected $_commands = array();
    protected $_url = '';
    /**
     * @var \Alib\Structure
     */
    protected $_structure = null;

    protected $_root = false;
    /**
     * @var LevelIndex
     */
    protected $_levelIndex = null;
    /**
     * @param string $path
     * @param array $commands
     * @param \Alib\Structure $structure
     */
    public function __construct($path, $commands, $structure)
    {
        $this->_path      = $path;
        $this->_commands  = $commands;
        $this->_structure = $structure;
        $this->_url = implode('/', $commands);
        if (!$this->_url)
            $this->_root = true;
    }

    /**
     * Выбрать запрос для этого уровня.
     *
     * Запрос - это команты, рзделенные "/"
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    public function getConfigObject($name, $section = 'base')
    {
        $name_full = $name . '***'. $section;
        if (isset($this->_configObjects[$name_full]))
            return $this->_configObjects[$name_full];
        $path = $this->_path . '/' . $name . '.ini';
        if (is_file($path))
            $this->_configObjects[$name_full] = new \Zend_Config_Ini($path, $section);
        else
            $this->_configObjects[$name_full] = null;

        return $this->_configObjects[$name_full];
    }

    /**
     * Выбрать полное имя конфигурационного файла.
     *
     * @param string $name имя файла без расширения
     * @return null|string
     */
    public function getConfigFileFullName($name = 'config')
    {
        $path = $this->_path . '/' . $name . '.ini';
        if (is_file($path))
            return $path;
        else
            return null;

    }

    public function processedRouter()
    {
        $routerProcess = new Routes($this, $this->_structure);
        return $routerProcess->process();
    }

    /**
     *
     * Возврат обработчика index.php для уровня.
     *
     * @return LevelIndex
     */
    public function getLevelIndex()
    {
        if ($this->_levelIndex)
            return $this->_levelIndex;
        $this->_levelIndex = new LevelIndex($this);
        $this->_levelIndex->process();
        return $this->_levelIndex;
    }

    /**
     * Возврат пути до папки уровня.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * @param string $name имя файла без расширения
     * @param string $extension ресширения файла без точки
     * @return string|null
     */
    public function getString($name, $extension = 'html')
    {
        $name_full = $name . '.'. $extension;
        if (isset($this->_strings[$name_full]))
            return $this->_strings[$name_full];
        $path = $this->_path . $name_full;
        if (is_file($path))
            $this->_strings[$name_full] = file_get_contents($path);
        else
            $this->_strings[$name_full] = null;
        return $this->_strings[$name_full];
    }
}
