<?php

/**
 * Общий класс для класов загрузки и показа картинок и превью.
 * 
 * Пока в базовом, общем варианте. В дальшейшем возможно добавление адаптеров.
 * 
 * 
 * Версии:
 *  2011-08-25 Создание для простого использование
 * 
 * 
 */
namespace Alib\Image\AbstractClass;
use Alib\Params;
use Alib\Exception;
abstract class Common
{
    protected $_path = null;
    
    protected $_pathToOriginal = 'original';
    protected $_pathToPreview  = 'preview';

    /**
     *
     * @var \Dune_Image_Info
     */
    protected $_file = null;
    
    protected $_directoryLevel = 3;
    
    protected $_preview = array();
    
    protected $_name = '';
    protected $_nameWithSubfolders = '';

    protected $_extension = null;

    protected $_makeBaseDir = false;

    public function __construct($type = null, $realization = 'base', $makeDir = true)
    {
        $this->_makeBaseDir = $makeDir;
        if (!$realization)
            $realization = 'base';
        if ($type === null)
        {
            $this->setStoreDirectory('images/common');
        }
        else
        {
            $this->setStoreDirectory('images/' . $type . '/' . $realization);
        }
    }
    
    public function setStoreDirectory($path)
    {
        $pars = Params::getInstance();
        $file_folder = $pars->getFilesPathForSystem();
        $pathLocal = $path;
        $path = $file_folder . trim($path, ' /');
        if (!is_dir($path))
        {
            if ($this->_makeBaseDir)
            {
                $makeDir = new \Alib\Directory\Make($file_folder, true);
                $makeDir->setTargetDirectoryName($pathLocal)->make();
            }
            else
                throw new Exception('Папки ' . $path . ' не существует, а она нужна.', 1000);
        }
        $this->_path = $path;
        $this->_pathToOriginal = $path . '/' . $this->_pathToOriginal;
        $this->_pathToPreview  = $path . '/' . $this->_pathToPreview;
        if (!is_dir($this->_pathToOriginal))
            mkdir ($this->_pathToOriginal);
        if (!is_dir($this->_pathToPreview))
            mkdir ($this->_pathToPreview);
        return $this;
    }

    /**
     * Уставновка целевого файла.
     */
    public function setFile($file)
    {
        if (is_string($file))
            $file = new \Dune_Image_Info($file);
        $this->_file = $file;
        return $this;
    }
    
    
    protected function _makeSubfolderArray($string)
    {
        $array = array();
        $nakopitel = '';
        for($x = 0; $x < $this->_directoryLevel; $x++)
        {
            $nakopitel .= substr($string, $x, 1);
            $array[] = $nakopitel;
        }
        return $array;
    }


    protected function _buildSubfolder($path, $path_array)
    {
        foreach($path_array as $value)
        {
            $path .= '/' . $value;
        }
        return $path;
    }
    
    /**
     *  Создание имени файла с поддиректориями.
     * 
     * @return string|boolean
     */
    protected function _buildSubfolderWithName()
    {
        if ($this->_nameWithSubfolders)
            return $this->_nameWithSubfolders;
        if (!$this->_name)
            return false;
        $path = $this->_makeSubfolderArray($this->_name);
        return $this->_nameWithSubfolders = ltrim($this->_buildSubfolder('', $path), '/') . '/' . $this->_name;
    }

    

    /**
     * Возврат специального имени для вставку.
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Установка имени файла для сохранения и для формирования путей к существующему файлу.
     *
     * Уставка без расширения.
     *
     * @param $name
     * @return Common
     */
    public function setName($name)
    {
        $this->_nameWithSubfolders = '';
        $this->_name = $name;
        return $this;
    }

    public function setExtension($string)
    {
        $this->_extension = $string;
        return $this;
    }


}

