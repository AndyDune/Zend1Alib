<?php
/**
 * V.02
 * 
 * Выборка ссылку на картинки
 * 
 * 
 * Не происходит контроля налиция картинок и папок - изменить в будущем.
 * 
 * Версии:
 *  2011-08-25 Создание для простого использование
 * 
 * 
 * 
 */
namespace Alib\Image;
use Alib\Params;
class Display extends AbstractClass\Common
{
    protected $_pathBrowser = null;
    protected $_pathToOriginalBrowser = '';
    protected $_pathToPreviewBrowser  = '';
    
    protected $_previewFolders = null;
    protected $_previewFoldersParse = null;
    

    public function setStoreDirectory($path)
    {
        $path_t = $path;
        $pars = Params::getInstance();
        $file_folder = $pars->getFilesPathForBrowser();
        $path = $file_folder . trim($path, ' /');
        $this->_path = $path;
        $this->_pathToOriginalBrowser = $path . '/' . $this->_pathToOriginal;
        $this->_pathToPreviewBrowser  = $path . '/' . $this->_pathToPreview;

        parent::setStoreDirectory($path_t);

        return $this;
    }


    public function getUrl($name = null)
    {
        if ($name)
            $this->setName($name);
        return $this->_pathToOriginalBrowser . '/' . $this->_buildSubfolderWithName();
    }

    /**
     * Вибрать минимальную превьюшку из всех возможных
     * 
     * Сравнивает по ширине.
     * 
     * @param type $name
     * @return type 
     */
    public function getPreviewMin($name = null)
    {
        if ($name)
            $this->setName($name);
        $pre_folders = $this->_getPreviewFolders(true);
        $count = count($pre_folders);
        if (!$pre_folders or !$count)
            return false;
/*        
        if($count == 1)
        {
            return $this->getPreview(current($pre_folders));
        }
*/        
        $min = array('x' => 500000, 'def' => true);
//        array_shift($pre_folders);
        foreach($pre_folders as $run)
        {
            if ($run['x'] < $min['x'])
            {
                if ($this->checkPreview($run))
                    $min = $run;
            }
        }
        if (isset($min['def']))
            return $this->getUrl();
        return $this->getPreview($min);
    }    
    
    
    protected function _getPreviewFolders($parse = false)
    {
        if (!$this->_pathToPreview)
            return false;
        // Строим массив с превьюх-папками
        if (!$this->_previewFolders)
        {
            $data = array();
            $dirs = new \DirectoryIterator($this->_pathToPreview);
            foreach ($dirs as $dir)
            {
                if ($dir->isDot())
                    continue;
                if ($dir->isDir())
                {
                    $data[] = $dir->getFilename();
                }
            }
            $this->_previewFolders = $data;
        }
        if (!$parse)
            return $this->_previewFolders;
        if (!$this->_previewFoldersParse)
        {
            $data = array();
            foreach ($this->_previewFolders as $dir)
            {
                $parts_1 = explode('_', $dir);
                if (count($parts_1) < 3)
                    continue;
                $xy = explode('x', $parts_1[0]);
                if (count($xy) < 2)
                    continue;
                $one = array(
                    'x'         => $xy[0],
                    'y'         => $xy[1],
                    'xy'        => $parts_1[0],
                    'mode'      => $parts_1[1],
                    'color'     => $parts_1[2],
                    'directory' => $dir,
                );
                $data[] = $one;
            }
            $this->_previewFoldersParse = $data;
        }
        return $this->_previewFoldersParse;
    }

    /**
     * Проверка на существование превьюшки
     * 
     * @param type $size
     * @param type $mode
     * @param type $color
     * @return type 
     */
    public function getPreview($size, $mode = 'add', $color = 'ffffff')
    {
        if (!$this->_name)
            throw new \Exception('не установлен');
        if (!is_array($size))
        {
            $xy = explode('x', $size);
            if (count($xy) == 1)
            {
                //$size = $xy[0] . 'x' . $xy[0];
                $size = $xy[0];
                $xy[1] = $xy[0];
            }
            if (count($xy) > 2)
               throw new Exception('Неверно преданный параметр $size.', 1000); 
            if (!in_array($mode, array('add', 'cut')))
               throw new Exception('Неверно преданный параметр $mode.', 1000); 

            $data = array(
                'x'         => $xy[0],
                'y'         => $xy[1],
                'mode'      => $mode,
                'color'     => $color,
                'directory' => $size . '_' . $mode . '_'. $color,
            );
        }
        else
        {
            $data = $size;
        }
        return $this->_pathToPreviewBrowser . '/' . $data['directory'] . '/' . $this->_buildSubfolderWithName();
    }
    
    /**
     * Выборка конкретного превью
     * 
     * @param type $size
     * @param type $mode
     * @param type $color
     * @return type 
     */
    public function checkPreview($size, $mode = 'add', $color = 'ffffff')
    {
        if (!$this->_name)
            throw new Exception('не установлен');
        if (!is_array($size))
        {
            $xy = explode('x', $size);
            if (count($xy) != 2)
               throw new Exception('Неверно преданный параметр $size.', 1000); 
            if (!in_array($mode, array('add', 'cut')))
               throw new Exception('Неверно преданный параметр $mode.', 1000); 

            $data = array(
                'x'         => $xy[0],
                'y'         => $xy[1],
                'mode'      => $mode,
                'color'     => $color,
                'directory' => $size . '_' . $mode . '_'. $color,
            );
        }
        else
        {
            $data = $size;
        }
        $path =  $this->_pathToPreview . '/' . $data['directory'] . '/' . $this->_buildSubfolderWithName();
        if (is_file($path))
        {
            return $path;
        }
        return false;
            
    }    
    
    
}
