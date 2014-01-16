<?php
/**
 * Класс инкапсулирует в себе массив с параметрами загруженного файла
 * Сохраняет расшифровку кодов ошибок. Доступ по ключу "err_name"
 * 
 * Реализует интерфейсы: ArrayAccess
 * Определены волшебныек методы: __set, __get
 * 
 * ----------------------------------------------------
 * | Библиотека: Dune                                  |
 * | Файл: File.php                                    |
 * | В библиотеке: Dune/Upload/File.php                |
 * | Автор: Андрей Рыжов (Dune) <dune@rznw.ru>         |
 * | Версия: 1.02                                      |
 * | Сайт: www.rznw.ru                                 |
 * ----------------------------------------------------
 *
 * Версии:
 * 1.03 (2012-09-27)
 * Конструктор может принимать не ключ для извлечения параметров, а массив параметров.
 *
 * Версия 1.01 -> 1.02
 * Добавлены интерфейсные методы.
 * 
 * 
 */
namespace Alib\Upload;
class File extends \Dune_Upload_File
{
    public function __construct($name)
    {
        if (is_array($name))
        {
            $this->file = $name;
        }
        else
        {
            $this->formFieldName = $name;
            if (isset($_FILES[$name]) and is_array($_FILES[$name]))
            {
                $this->file = $_FILES[$name];
            }
        }

        if ($this->file and count($this->file))
        {
            $this->haveUpload = true;
            switch ($this->file['error'])
            {
                case 0:
                    $this->correctUpload = true;
                    $this->file['err_name'] = 'Ошибок не было, файл загружен';
                    break;
                case 1:
                    $this->file['err_name'] = 'Размер загруженного файла превышает размер установленный параметром upload_max_filesize в php.ini';
                    break;
                case 2:
                    $this->file['err_name'] = 'Размер загруженного файла превышает размер установленный параметром MAX_FILE_SIZE в HTML форме';
                    break;
                case 3:
                    $this->file['err_name'] = 'Загружена только часть файла';
                    break;
                case 4:
                    $this->file['err_name'] = 'Файл не был загружен (Пользователь в форме указал неверный путь к файлу)';
            }
        }
    }

}