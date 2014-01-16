<?php


namespace Alib\Form\Element;
use Alib\Validate;
class FileImage extends AbstractClass\File
{
    /**
     *
     * @var Image_Store
     */
    protected $_saveAdapter = null;
    
    protected function _init()
    {
         $valid = new Validate\File\Image();
         $this->addValidator($valid);
         if ($val = $this->_builder->getMaxImageSize())
         {
             $valid = new Validate\File\MaxImageSize();
             $valid->setSize($val);
             $this->addValidator($valid);
         }
         
         if ($val = $this->_builder->getFileStoreAdapter())
         {
             $this->_saveAdapter = $val;
         }
    }    
    
    /**
     * Processes the file, returns null or the filename only
     * For the complete path, use getFileName
     *
     * @return null|string
     */
    public function getValue()
    {
        if ($this->_value !== null) {
            return $this->_value;
        }

        $content = $this->getTransferAdapter()->getFileName($this->getName());
        if (empty($content)) {
            return null;
        }

        if (!$this->isValid(null)) {
            return null;
        }

        if (!$this->_valueDisabled && !$this->receive()) {
            return null;
        }

        if ($this->_saveAdapter !== null)
        {
            $this->_saveAdapter->setFile($content);
            $this->_saveAdapter->save();
            return $name = $this->_saveAdapter->getName();
        }

        return $content;
        return $this->getFileName(null, false);
    }    
    
}