<?php
/**
 * Класс инкапсулирует в себе массив с параметрами загруженных файлов в пакете: $_FILES[имя пакета]
 * Сохраняет расшифровку кодов ошибок. Доступ по ключу "err_name"
 * 
 * Реализует интерфейсы: ArrayAccess
 * Определены волшебныек методы: __set, __get
 * 
 * ----------------------------------------------------
 * | Библиотека: Dune                                  |
 * | Файл: FileMulti.php                               |
 * | В библиотеке: Dune/Upload/FileMulti.php           |
 * | Автор: Андрей Рыжов (Dune) <dune@rznw.ru>         |
 * | Версия: 1.01                                      |
 * | Сайт: www.rznw.ru                                 |
 * ----------------------------------------------------
 * 
 * Версия 1.01 (2009-09-03)
 * Контроль за наличием имени файла в запросе.
 * 
 */
namespace Alib\Upload;
class FileMulti extends \Dune_Upload_FileMulti
{
    protected $_fileObjects = [];

    public function __construct($name)
    {
        $this->formFieldName = $name;
        if (isset($_FILES[$name]) and is_array($_FILES[$name]))
        {
            $this->haveUpload = true;
            $_files = $_FILES[$name];
            $this->fileArray = array();
            if (is_array($_files['name']))
            { // 3
                foreach ($_files['name'] as $key => $value)
                { // 2
                    if (!$value) continue;
                    $fileArray = [];
                    $fileArray['name'] 		= $_files['name'][$key];
                    $fileArray['type'] 		= $_files['type'][$key];
                    $fileArray['tmp_name'] 	= $_files['tmp_name'][$key];
                    $fileArray['error']     = $_files['error'][$key];
                    $fileArray['size']     	= $_files['size'][$key];

                    $this->fileArray[$key] = new File($fileArray);
                } // 2
                $this->countFiles = count($this->fileArray);
                $this->currentFile = key($this->fileArray);
                $this->file = current($this->fileArray);
            } // 3
        }
    }
}