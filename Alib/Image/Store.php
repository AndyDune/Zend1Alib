<?php

/**
 * Сохранение картинки с созданием превьюшек.
 * 
 * Пока в базовом, общем варианте. В дальшейшем возможно добавление адаптеров.
 * 
 * 
 */
namespace Alib\Image;
use Alib\String;
use Alib\Exception;
class Store extends AbstractClass\Common
{
    protected $_nameToDelete = '';
    protected $_nameToDeleteNoEx = false;

    protected $_posibleEx = array('.jpg', '.gif', '.png');

    public function addPreview($size, $mode = 'add', $color = 'ffffff')
    {
        $xy = explode('x', $size);
        if (count($xy) != 2)
        {
            $xy[1] = 0;
           //throw new Exception('Неверно преданный параметр $size.', 1000);
        }
        if ($xy[0] == 0)
        {
            $xy[0] = $xy[1];
            $xy[1] = 0;
        }
        if (!in_array($mode, array('add', 'cut')))
           throw new Exception('Неверно преданный параметр $mode.', 1000); 
        
        $data = array(
            'x'         => $xy[0],
            'y'         => $xy[1],
            'mode'      => $mode,
            'color'     => $color,
            'directory' => $size . '_' . $mode . '_'. $color,
        );
        $this->_preview[] = $data;
        return $this;
    }

    public function setPreviews($size, $mode = 'add', $color = 'ffffff')
    {
        foreach($size as $value)
        {
            $this->addPreview($value[0], $value[1], $value[2]);
        }
        return $this;
    }


    /**
     * Сохранение на диск загруженной картинки с созданием указанных превьюшек.
     */
    public function save()
    {
        $name = $this->_name;
        if (!$name)
            throw new Exception('Не установлено имя результативного файла.', 1000);

        /*
        $gen_name = new String\GenerateRandom();
        $name = $gen_name->get();
        */
        $subfolder_array = $this->_makeSubfolderArray($name);
        
        $path_to_image = $this->_makeSubfolder($this->_pathToOriginal, $subfolder_array);
        
        //$this->_name =
        $file_name = $name;
        if (!strpos($file_name, '.'))
            $file_name = $file_name . $this->_file->getExtension();
        
        if ($this->_nameToDelete)
            $this->_delete ($this->_nameToDelete);
        
        
        $result = copy($this->_file->getPath(), $path_to_image . '/' . $file_name);
        // Создаем превью
        if ($result and count($this->_preview))
        {
            foreach($this->_preview as $run)
            {
                $this->_savePreview($run, $file_name, $subfolder_array);
            }
        }
        return $result;
    }

    /**
     * Создание превью картинок отдельно от сохранение основного файла.
     *
     * @return bool
     */
    public function createPreview()
    {
        if (!$this->_file)
            return false;
        foreach($this->_preview as $run)
        {
            $name = $this->_file->getFileName();
            $file_name = $name . $this->_file->getExtension();
            $subfolder_array = $this->_makeSubfolderArray($name);
            $this->_savePreview($run, $file_name, $subfolder_array);
        }
    }

    protected function _savePreview($data, $filename, $subfolder_array)
    {
        $filename = explode('.', $filename);
        $filename = $filename[0];
        array_unshift($subfolder_array, $data['directory']);
        $path_to_image = $this->_makeSubfolder($this->_pathToPreview, $subfolder_array);
        
        if ($this->_file->getWidth() >= $this->_file->getHeight())
        {
            $modetumb = 'width';
            $size = $data['x'];
        }
        else
        {
            $modetumb = 'height';
            $size = $data['y'];
        }

        $trans = new \Dune_Image_Transform($this->_file);
        $trans->setPathToResultImage($path_to_image);

        if ($data['y'])
        {
            $trans->setModeThumb($modetumb);

            $trans->setProportions($data['x'], $data['y']);
            $trans->setModeProportion($data['mode']);
            $trans->changeProportion();
        }
        else
        {
            $size = $data['x'];
            $trans->setModeThumb('both');
        }

        //if ($size > 300)
        //    \Alib\Test::pr($data, 1);

        $trans->createThumb($size);

        $trans->save($filename);
        return true;
        
    }
    
    public function toDelete($name, $ex = null)
    {
        $this->_nameToDeleteNoEx = false;
        if ($ex)
            $name = $name . '.' . $ex;
        else if (!strpos($name, '.'))
            $this->_nameToDeleteNoEx = true;

        $this->_nameToDelete = $name;
        return $this;
    }
    

    protected function _makeSubfolder($path, $path_array)
    {
        foreach($path_array as $value)
        {
            $path .= '/' . $value;
            if (!is_dir($path))
                mkdir ($path);
        }
        return $path;
    }
    

    /**
     * Отмена сохранения файлов.
     * Происходит удаление основного файла и всех созданных превьюшек.
     */
    public function cancel()
    {
        $this->_delete($this->_name);
        return $this;
    }

    /**
     * Отмена сохранения файлов.
     * Происходит удаление основного файла и всех созданных превьюшек.
     */
    public function delete()
    {
        $parts = explode('.', $this->_name);
        if (count($parts) < 2)
            $this->_nameToDeleteNoEx = true;
        else
            $this->_nameToDeleteNoEx = false;
        $this->_delete($this->_name);
        return $this;
    }
    
    /**
     */
    protected function _delete($name)
    {
        $subfolder_array = $this->_makeSubfolderArray($name);
        
        // Удаление оригинальной картинки
        $path_to_image = $this->_buildSubfolder($this->_pathToOriginal, $subfolder_array);
        $file_name = $path_to_image . '/' . $name;
        $this->_tryToDelete($file_name);

        // Удаление превьюх
        $dirs = new \DirectoryIterator($this->_pathToPreview);
        foreach ($dirs as $dir)
        {
            if ($dir->isDot())
                continue;
            if ($dir->isDir())
            {
                $current_dir = $this->_pathToPreview . '/' . $dir->getFilename();
                $path_to_image = $this->_buildSubfolder($current_dir, $subfolder_array);
                $file_name = $path_to_image . '/' . $name;
                $this->_tryToDelete($file_name);
            }
        }
        
        return $this;
    }

    protected function _tryToDelete($name)
    {
        if ($this->_nameToDeleteNoEx)
        {
            foreach($this->_posibleEx as $value)
            {
                $nameToTry = $name . $value;
                if (is_file($nameToTry))
                {
                    @unlink($nameToTry);
                    continue;
                }

            }
        }
        else
        {
            if (is_file($name))
                @unlink($name);
        }
        return $this;
    }
    
    
}
