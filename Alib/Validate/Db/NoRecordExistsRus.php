<?php

/**
 * Аналог Zend_Validate_Db_NoRecordExists только с возможностью проверять lower значения.
 * Заменяет при тесте "ё" на "е"
 * 
 */
namespace Alib\Validate\Db;
class NoRecordExistsRus extends \Zend_Validate_Db_Abstract
{
    protected $_checkLower = false;
    protected $_encoding = 'UTF-8';

    public function isValid($value)
    {
        $valid = true;
        
        // Проверять ли Lower значения
        if ($this->_checkLower)
            $value = $this->_toLower($value);
             
        if ($this->_encoding !== null) 
            $value = preg_replace('|ё|u', 'е', $value);
        else
            $value = preg_replace('|ё|', 'е', $value);
        
        $this->_setValue($value);

        $result = $this->_query($value);
        if ($result) 
        {
            $valid = false;
            $this->_error(self::ERROR_RECORD_FOUND);
        }

        return $valid;
    }
    
    
    /**
     * Set the input encoding for the given string
     *
     * @param  string $encoding
     * @return Zend_Filter_StringToLower Provides a fluent interface
     * @throws Zend_Filter_Exception
     */
    public function setEncoding($encoding = null)
    {
        if ($encoding !== null) {
            if (!function_exists('mb_strtolower')) {
                throw new Zend_Filter_Exception('mbstring is required for this feature');
            }

            $encoding = (string) $encoding;
            if (!in_array(strtolower($encoding), array_map('strtolower', mb_list_encodings()))) {
                throw new Zend_Filter_Exception("The given encoding '$encoding' is not supported by mbstring");
            }
        }

        $this->_encoding = $encoding;
        return $this;
    }    
    
    public function setCheckLower($value = true)
    {
        $this->_checkLower = $value;
        return $this;
    }
    
    protected function _toLower($value)
    {
        if ($this->_encoding !== null) {
            return mb_strtolower((string) $value, $this->_encoding);
        }

        return strtolower((string) $value);
    }    
}
