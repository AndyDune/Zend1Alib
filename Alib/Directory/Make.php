<?php
/**
 * User: Dune
 * Date: 04.05.12
 * Time: 9:34
 *
 * Создание диретории
 */
namespace Alib\Directory;
use Alib\Exception;
class Make
{

    protected $_root = '';
    protected $_directoryName = '';
    protected $_directoryNameArray = array();
    protected $_directoryNameArrayFromLevels = array();

    protected $_isDir = false;

    protected $_levelsForTargetDirectoryName    = 0;
    protected $_fillerForTargetDirectoryName    = '0';
    protected $_backwardsForTargetDirectoryName = false;

    /**
     * Принимает имя директории от корня сайта.
     *
     * @param string $name имя директории от корня сайта !
     * @param boolean $createIfNotExist создавать ли корневую директорию если нет ее
     */
    public function __construct($root, $createIfNotExist = false)
    {
        $this->_root = rtrim($root, '/');
        if (!is_dir($this->_root))
        {
            if (!$createIfNotExist)
                goto to_exception;
            if (!mkdir($this->_root))
            {
                to_exception:
                throw new Exception('Корневая директория для создания новой директории не сущетсвует: ' . $this->_root, 1);
            }
        }
    }

    /**
     * Передача имени папки для создания.
     * Папка может быть создана с учетом родительских - создатся дерево папок.
     * Создание дополнительных уровней производится из имени последней папки.
     *
     * @param $name имя папки для создания, пожет быть с подпарками
     * @return Make
     */
    public function setTargetDirectoryName($name)
    {
        $parts = explode('/', rtrim($name, '/'));
        $maxIndex = count($parts) - 1;

        $this->_directoryName = $parts[$maxIndex];
        if ($maxIndex)
        {
            array_pop($parts);
            $this->_directoryNameArray = $parts;
        }
        return $this;
    }


    public function setLevelsFromTargetDirectoryName($level, $filler = '0', $backwards = false)
    {
        $this->_levelsForTargetDirectoryName    = (int)$level;
        $this->_fillerForTargetDirectoryName    = $filler;
        $this->_backwardsForTargetDirectoryName = $backwards;
        return $this;
    }

    public function make()
    {
        if (!$this->_directoryName)
        {
            throw new Exception('Ну установлено имя целевой директории (метод setTargetDirectoryName)', 1);
        }

        if ($this->_levelsForTargetDirectoryName)
        {
            $this->_buildLevelsForTargetDirectory();
        }

        $cumul = $this->_root;
        foreach ($this->_directoryNameArray as $value)
        {
            $cumul .= '/' . $value;
            $this->_createDirAndCheck($cumul);
        }

        if ($this->_levelsForTargetDirectoryName)
        {
            $this->_buildLevelsForTargetDirectory();
            foreach ($this->_directoryNameArrayFromLevels as $value)
            {
                $cumul .= '/' . $value;
                $this->_createDirAndCheck($cumul);
            }
        }
        $cumul .= '/' . $this->_directoryName;
        $this->_createDirAndCheck($cumul);
        return $cumul;
    }

    protected function _buildLevelsForTargetDirectory()
    {
        $this->_directoryNameArrayFromLevels = array();
        $directoryLength = strlen($this->_directoryName);
        // Если имя директории короче числа уровней
        $cumul = '';
        if ($directoryLength < $this->_levelsForTargetDirectoryName)
        {
            $levelsToExtractFromName = $directoryLength;
            $this->_directoryNameArrayFromLevels[] = $cumul = $this->_fillerForTargetDirectoryName;
        }
        else
            $levelsToExtractFromName = $this->_levelsForTargetDirectoryName;

        for($x = 1; $x <= $levelsToExtractFromName; $x++)
        {
            $this->_directoryNameArrayFromLevels[] = $cumul . substr($this->_directoryName, 0, $x);
        }

    }

    protected function _createDirAndCheck($fullName)
    {
        if (is_dir($fullName))
            return true;
        if (!mkdir($fullName))
        {
            throw new Exception('Не удалось создать директорию: ' . $fullName, 1);
        }
        return false;
    }

}
