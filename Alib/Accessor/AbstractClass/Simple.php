<?php

/**
 * Реализация универсльного эксессора для быстрого построения.
 * 
 */
namespace Alib\Accessor\AbstractClass;

abstract class Simple
{
    protected $_accessData = array();
    public function __call($method, $args)
    {
        if (substr($method, 0, 3) == 'set')
        {
            $key = strtolower(substr($method, 3));
            $this->_accessData[$key] = $args[0];
            return $this;
        }
        if (substr($method, 0, 3) == 'get')
        {
            $key = strtolower(substr($method, 3));
            if (key_exists($key, $this->_accessData))
                return $this->_accessData[$key];
            return null;
        }
        throw new \Alib\Exception('Metode is not exist', 1);
    }
}
