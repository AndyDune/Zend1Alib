<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 12.09.12
 * Time: 16:57
 *
 * Разбиение строки на массив.
 * Надстройка над explode с контролем пустых значений и подсчет результатов.
 *
 */
namespace Alib\String;
class Explode implements \ArrayAccess, \Iterator, \Countable
{
    protected $_string;
    protected $_separator;
    protected $_array = array();
    protected $_count = 0;
    protected $_empty = false;


    public function __construct($string, $separator = '', $key_begin = 0)
    {
		$this->_string = trim($string, ' '.$separator);
        $this->_separator = $separator;
        if ($this->_separator)
            $this->make($key_begin);

    }

    public function setSeparator($separator)
    {
        $this->_separator = $separator;
    }

    public function count()
    {
        return $this->_count;
    }

    public function leaveEmpty($bool = true)
    {
        $this->_empty = $bool;
    }

    public function getInteger($key = 0, $default = null)
    {
        if (isset($this->_array[$key]))
            return (int)$this->_array[$key];
        else
            return $default;
    }

    /**
     * Преобразует стоку в массив по разделителю. Удаляет пустые.
     *
     * @return integer
     */
    public function make($key_begin = 0)
    {
        $array_result = array();
        if ($this->_string)
        {
            $array_begin = explode($this->_separator, $this->_string);
            foreach ($array_begin as $value)
            {
                $x = trim($value);
                if ($x != '' or $this->_empty)
                {
                    $array_result[$key_begin] = $x;
                    $key_begin++;
                }
            }
        }
        $this->_array = $array_result;
        $this->_count = count($array_result);

        return $this->_count;
    }

    /**
     * Возврат всего результирующего массива.
     *
     * @param boolean $in_container
     * @return array
     */
    public function getResultArray($in_container = false)
    {
        if ($in_container)
            return new Dune_Array_Container($this->_array);
        return $this->_array;
    }


//////////////////////////////////////////////////////////////////
///////////////////////////////     Методы интерфейса ArrayAccess
    public function offsetExists($key)
    {
        return isset($this->_array[$key]);
    }

    public function offsetGet($key)
    {
        if (isset($this->_array[$key]))
            return $this->_array[$key];
        else
            return null;
    }

    public function offsetSet($key, $value)
    {
        $this->_array[$key] = $value;
    }

    public function offsetUnset($key)
    {
        unset($this->_array[$key]);
    }

    ////////////////////////////////////////////////////////////////
///////////////////////////////     Методы интерфейса Iterator
    // устанавливает итеретор на первый элемент
    public function rewind()
    {
        return reset($this->_array);
    }

    // возвращает текущий элемент
    public function current()
    {
        return current($this->_array);
    }

    // возвращает ключ текущего элемента
    public function key()
    {
        return key($this->_array);
    }

    // переходит к следующему элементу
    public function next()
    {
        return next($this->_array);
    }

    // проверяет, существует ли текущий элемент после выполнения мотода rewind или next
    public function valid()
    {
        return isset($this->_array[key($this->_array)]);
    }
/////////////////////////////
////////////////////////////////////////////////////////////////


}


