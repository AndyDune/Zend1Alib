<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 25.09.12
 * Time: 14:29
 *
 * Тип данных галерея.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Upload\FileMulti;
use Alib\Image\Info;

class Gallery extends Record\DataType
{
    const TO_BIG_FILE    = 'TO_BIG';
    const TO_BIG         = 'TO_BIG';
    const NO_IMAGE       = 'NO_IMAGE';
    const TRANSFER_ERROR = 'TRANSFER_ERROR';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::TO_BIG       => "Картинка больше установленного разрешенного размера.",
        self::TO_BIG_FILE  => "Файл имеет размер больше разрешенного.",
        self::NO_IMAGE     => "Переданный файл не является картинкой",
        self::TRANSFER_ERROR  => "Ошибка приема файла.",
    );


    protected $_maxFileSize = null;
    protected $_maxImageSize = null;

    /**
     * Массив объектов Info
     *
     * @var Info
     */
    protected $_images = [];

    /**
     * Данные для новых картинок.
     *
     * @var array
     */
    protected $_imagesData = [];


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


    /**
     * @var \Application\Images\Record\Gallery\Gallery
     */
    protected $_recordGallery =  null;

    protected $_galleryRealization = 'base';

    protected $_titleFromRecord = null;


    public function init()
    {
        return $this;
    }


    protected function _initSetRecord()
    {
        //$this->_imageStore   = new Store('gallery', $this->_record->getRealization());
        //$this->_imageDisplay = new Display('gallery', $this->_record->getRealization());

//        $this->_recordGallery = $this->_record->getRecord('gallery', 'gallery', $this->_galleryRealization, 'Images');
//        $this->_recordGallery->retrieve($this->_value);
    }

    protected function _getRecordGallery()
    {
        if (!$this->_recordGallery)
        {
            $this->_recordGallery = $this->_record->getRecord('gallery', 'gallery', $this->_galleryRealization, 'Images');
            $valueCurrent = $this->_record->getDataRetrieved($this->_field);
            if ($valueCurrent)
                $this->_recordGallery->retrieve($valueCurrent);
        }
        return $this->_recordGallery;
    }

    public function getGallery()
    {
        return $this->_getRecordGallery();
    }

    public function addPreview($size, $mode = 'add', $color = 'ffffff')
    {
        $this->_previews[] = [$size, $mode, $color];
        $this->_getRecordGallery()->addPreview($size, $mode, $color);
        return $this;
    }

    /**
     * Установка заголовка галереи из родительской записи.
     *
     * @param $field поле записи из которой берется значение.
     */
    public function setTitleFromRecord($field)
    {
        $this->_titleFromRecord = $field;
        return $this;
    }

    public function setMaxFileSize($size)
    {
        $this->_maxFileSize = $size;
        return $this;
    }

    public function setRealization($realization = 'base')
    {
        $this->_galleryRealization = $realization;
        return $this;
    }

    public function setMaxImageSize($size)
    {
        $this->_maxImageSize = $size;
        return $this;
    }

    public function _processReady()
    {
        $nameData = '__' . $this->_field;
        $filesUpload = new FileMulti($this->_field);

        $objectsList = $filesUpload->getList();
        foreach($objectsList as $key => $fileUpload)
        {
            // Ничего не загружено
            if (!$fileUpload->uploaded())
                continue;

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


            $this->_imagesData[$key] = [];
            if (isset($_POST[$nameData]['title'][$key]))
            {
                $this->_imagesData[$key]['title'] = $_POST[$nameData]['title'][$key];
            }

            $this->_images[$key] = $fileInfo;
        }
//        \Alib\Test::pr($_POST);
//        \Alib\Test::pr($this->_imagesData);
//        \Alib\Test::pr($this->_images, 1);
        return true;
    }

    /**
     * Процесс сохранения галереи с обновлением информции в поле от галереи.
     * Поле хранит id связной галереи.
     *
     * @return bool|void
     */
    public function processAfterValidateSuccess()
    {
        $this->_getRecordGallery();
        $valueCurrent = $this->_record->getDataRetrieved($this->_field);
        $this->_recordGallery->retrieve($valueCurrent);
        // Нет галереи и нет картинок для галереи
        if (!$this->_recordGallery->getId() and !count($this->_images))
            return false;
        foreach($this->_images as $key => $image)
        {
            $this->_recordGallery->addImage($image, $this->_imagesData[$key]);
        }

        if ($this->_titleFromRecord)
            $this->_recordGallery->title = $this->_dataObject[$this->_titleFromRecord];
        $this->_recordGallery->save();

        $this->_value = $this->_recordGallery->getId();
        return true;

    }
}


