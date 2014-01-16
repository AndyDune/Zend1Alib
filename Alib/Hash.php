<?php

/**
 * Генерация хеша сироки с использованием затравки.
 * Сравнение отхерированной строки с новой, предложенной.
 * 
 * Истоия:
 * 2011-06-11 Создан.
 * 
 */
namespace Alib;
class Hash
{
    /**
     * Длинна затравки
     * 
     * @var integer 
     */
    protected $_saltLength = 6;
    
    protected $_stringOrigin = '';
    protected $_stringHashed = '';
    
    public function __construct($string = null, $string_hashed = null)
    {
        if ($string)
            $this->_stringOrigin = $string;
        if ($string_hashed)
            $this->_stringHashed = $string_hashed;
    }

    public function setHashedString($value)
    {
        $this->_stringHashed = $value;
    }

    public function hash($salt = null)
    {
        if ($salt === null)
        {
            if ($this->_saltLength)
                $salt = substr(md5(time()), 0, $this->_saltLength);
            else
                $salt = '';
        }
        return $salt . md5($salt . $this->_stringOrigin);
    }
    
    
    public function setSaltLength($value)
    {
        $this->_saltLength = $value;
    }

    public function compare()
    {
        if (!$this->_stringHashed or !$this->_stringOrigin)
            throw new Exception('Не инициилизированы обязательные переменные: $this->_stringHashed или $this->_stringOrigin');
        $hash_new = $this->hash($this->_getSaltFromHashed());
        if ($hash_new == $this->_stringHashed)
                return true;
        return false;
    }
    
    
    protected function _getSaltFromHashed()
    {
        if ($this->_saltLength)
            $salt = substr($this->_stringHashed, 0, $this->_saltLength);
        else
            $salt = '';
        
        return $salt;
    }
    
}
