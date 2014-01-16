<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 14.09.12
 * Time: 10:04
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Upload\File;
use Alib\Image\Info;
use Alib\Image\Store;
use Alib\Image\Display;
class Image extends Record\DataType
{
    const TO_BIG_FILE = 'TO_BIG';
    const TO_BIG = 'TO_BIG';
    const NO_IMAGE = 'NO_IMAGE';
    const TRANSFER_ERROR = 'TRANSFER_ERROR';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::TO_BIG       => "Картинка больше установленного разрешенного размера.",
        self::TO_BIG_FILE  => "Файл имеет размер больше разрешенного.",
        self::NO_IMAGE  => "Переданный файл не является картинкой",
        self::TRANSFER_ERROR  => "Ошибка приема файла.",
    );


    protected $_maxFileSize = null;
    protected $_maxImageSize = null;

    /**
     * @var Info
     */
    protected $_image = null;


    /**
     * @var Store
     */
    protected $_imageStore = null;


    /**
     * @var Display
     */
    protected $_imageDisplay = null;


    /**
     * Удалить картинку.
     * @var bool
     */
    protected $_commandDelete = false;


    protected $_previews = [];


    protected function _initSetRecord()
    {
        $this->_imageStore   = new Store('image', $this->_record->getRealization());
        $this->_imageDisplay = new Display('image', $this->_record->getRealization());
        $this->_imageDisplay->setName($this->_value);
    }

    public function addPreview($size, $mode = 'add', $color = 'ffffff')
    {
        $this->_previews[] = [$size, $mode, $color];
        $this->_imageStore->addPreview($size, $mode, $color);
        return $this;
    }

    public function getImageUrl()
    {
        return $this->_imageDisplay->getUrl($this->_value);
    }

    public function getImagePreview($preview = 0, $attr = [])
    {
        $url = $this->getImagePreviewUrl($preview);
        if (!$url)
            return $url;
        $str = '<img src="'. $url .'" ';
        foreach($attr as $key => $value)
        {
            $str .= $key . ' = "'. $value .'" ';
        }
        $str .= '/>';
        return $str;
    }

    public function getImagePreviewUrl($preview = 0)
    {
        if (!$this->_value)
            return null;
        $this->_imageDisplay->setName($this->_value);
        if (!count($this->_previews))
            return $this->getImageUrl();
        if (!isset($this->_previews[$preview]))
            $preview = 0;
        return $this->_imageDisplay->getPreview($this->_previews[$preview][0],
                                                $this->_previews[$preview][1],
                                                $this->_previews[$preview][2]
        );
    }


    public function setMaxFileSize($size)
    {
        $this->_maxFileSize = $size;
        return $this;
    }

    public function setMaxImageSize($size)
    {
        $this->_maxImageSize = $size;
        return $this;
    }


    public function _processReady()
    {
        $this->_checkToDelete();
        $fileUpload = new File($this->_field);
        // Ничего не загружено
        if (!$fileUpload->uploaded())
            return true;
        if (!$fileUpload->isCorrect())
        {
            $this->addMessage(self::TRANSFER_ERROR);
            return false;
        }

        if ($this->_maxFileSize and $this->_maxFileSize < $fileUpload->getSize())
        {
            $this->addMessage(self::TO_BIG_FILE);
            return false;
        }

        $fileInfo = new Info($fileUpload->getTmpName());
        if (!$fileInfo->isCorrect())
        {
            $this->addMessage(self::NO_IMAGE);
            return false;
        }

        if ($this->_maxImageSize and
            (
            $this->_maxImageSize < $fileInfo->getHeight()
            or
            $this->_maxImageSize < $fileInfo->getWidth()
            )
        )
        {
            $this->addMessage(self::TO_BIG);
            return false;
        }


        $this->_image = $fileInfo;
        return true;
    }


    /**
     * Удаление старой картинки и сохранение новой с модификацией.
     *
     * Если новой картинки нет старую не трогаем, но возможен вариант удаления старой без загрузки новой.
     *
     * Ключ $_POST[<поле>.command.delete] == true приказывает удалить картинку.
     *
     * @return Image
     */
    public function processAfterValidateSuccess()
    {
        $valueCurrent = $this->_record->getDataRetrieved($this->_field);
        if ($this->_commandDelete and $valueCurrent)
        {
            $this->_imageStore->setName($valueCurrent)->delete();
            $valueCurrent = false;
            if (!$this->_image)
                return true;
        }

        if (!$this->_image)
            return false;

        $str = new \Alib\String\GenerateRandom();
        $this->_value = $str->get() . $this->_image->getExtension();

        $this->_imageStore->setFile($this->_image->getPath())
                          ->setName($this->_value)
        ;
        if ($valueCurrent)
        {
            $this->_imageStore->toDelete($valueCurrent);
        }

        // Если сохранено успешно ставим статус и сохраняем его
        $this->_imageStore->save();
        return true;
    }

    protected function _checkToDelete()
    {
        $name = '__' . $this->_field;
        $valueCurrent = $this->_record->getDataRetrieved($this->_field);
        if ($valueCurrent and isset($_POST[$name]['delete']) and $_POST[$name]['delete'])
        {
            $this->_value = null;
            $this->_commandDelete = true;
        }
        else
        {
            $this->_value = $valueCurrent;
        }
    }

    public function __toString()
    {
        ob_start();
        ?><img width="100" src="<?= $this->getImagePreviewUrl(); ?>"><?
        return ob_get_clean();
    }

}
