<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 01.12.12
 * Time: 16:03
 *
 * Конвертирование объектра в массив и назад.
 *
 */

namespace Alib\ArrayClass;
class StdClass
{
    protected $_array = null;
    protected $_object = null;

    public function __construct($data)
    {
        if ($data)
        {
            if (is_array($data))
            {
                $this->_array = $data;
                $this->_object = $this->_toObject($data);
            }
            else if (is_object($data))
            {
                $this->_object = $data;
                $this->_array = $this->_toArray($data);
            }
        }
    }

    /**
     * @return object|null
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * @return array|null
     */
    public function getArray()
    {
        return $this->_array;
    }



    protected function _toArray($data)
    {
        if (is_object($data))
        {
            // Gets the properties of the given object
            // with get_object_vars function
            $data = get_object_vars($data);
        }

        if (is_array($data))
        {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return array_map([$this, __METHOD__], $data);
        }
        else
        {
            // Return array
            return $data;
        }
    }

    protected function _toObject($data)
    {
        if (is_array($data))
        {
            /*
            * Return array converted to object
            * Using __FUNCTION__ (Magic constant)
            * for recursive call
            */
            return (object) array_map([$this, __METHOD__], $data);
        }
        else
        {
            // Return object
            return $data;
        }
    }

}
