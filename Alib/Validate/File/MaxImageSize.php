<?php


/**
 * Проверяет соответствие картинки требованию к размеру.
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
namespace Alib\Validate\File;
class MaxImageSize extends \Zend_Validate_Abstract
{
    /**@#+
     * @const string Error constants
     */
    const BAD_IMAGE_SIZE = 'fileUploadImageErrorBadImageSIze';
    const UNKNOWN        = 'fileUploadImageErrorUnknown';
    
    /**@#-*/

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::BAD_IMAGE_SIZE => "Картинка '%value%' имеет недопустимое разрешение.",
        self::UNKNOWN        => "Unknown error while uploading file '%value%'"
    );

    /**
     * Internal array of files
     * @var array
     */
    protected $_files = array();
    
    
    protected $_size = 2000;

    /**
     * Sets validator options
     *
     * The array $files must be given in syntax of Zend_File_Transfer to be checked
     * If no files are given the $_FILES array will be used automatically.
     * NOTE: This validator will only work with HTTP POST uploads!
     *
     * @param  array|Zend_Config $files Array of files in syntax of Zend_File_Transfer
     * @return void
     */
    public function __construct($files = array())
    {
        if ($files instanceof \Zend_Config) {
            $files = $files->toArray();
        }

        $this->setFiles($files);
    }
    
    public function setSize($value)
    {
        $this->_size = (int)$value;
    }

            /**
     * Returns the array of set files
     *
     * @param  string $files (Optional) The file to return in detail
     * @return array
     * @throws Zend_Validate_Exception If file is not found
     */
    public function getFiles($file = null)
    {
        if ($file !== null) {
            $return = array();
            foreach ($this->_files as $name => $content) {
                if ($name === $file) {
                    $return[$file] = $this->_files[$name];
                }

                if ($content['name'] === $file) {
                    $return[$name] = $this->_files[$name];
                }
            }

            if (count($return) === 0) {
                throw new \Zend_Validate_Exception("The file '$file' was not found");
            }

            return $return;
        }

        return $this->_files;
    }

    /**
     * Sets the files to be checked
     *
     * @param  array $files The files to check in syntax of Zend_File_Transfer
     * @return Zend_Validate_File_Upload Provides a fluent interface
     */
    public function setFiles($files = array())
    {
        if (count($files) === 0) {
            $this->_files = $_FILES;
        } else {
            $this->_files = $files;
        }

        // see ZF-10738
        if (is_null($this->_files)) {
            $this->_files = array();
        }

        foreach($this->_files as $file => $content) {
            if (!isset($content['error'])) {
                unset($this->_files[$file]);
            }
        }

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the file was uploaded without errors
     *
     * @param  string $value Single file to check for upload errors, when giving null the $_FILES array
     *                       from initialization will be used
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        $this->_messages = null;
        if (array_key_exists($value, $this->_files)) {
            $files[$value] = $this->_files[$value];
        } else {
            foreach ($this->_files as $file => $content) {
                if (isset($content['name']) && ($content['name'] === $value)) {
                    $files[$file] = $this->_files[$file];
                }

                if (isset($content['tmp_name']) && ($content['tmp_name'] === $value)) {
                    $files[$file] = $this->_files[$file];
                }
            }
        }

        foreach ($files as $file => $content) 
        {
            $this->_value = $file;
            $image = new \Dune_Image_Info($content['tmp_name']);

            if ($image->getWidth() > $this->_size or $image->getHeight() > $this->_size)
            {
                $this->_throw($file, self::BAD_IMAGE_SIZE);
            }
        }

        if (count($this->_messages) > 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file) and !empty($file['name'])) {
                $this->_value = $file['name'];
            }
        }

        $this->_error($errorType);
        return false;
    }
}


