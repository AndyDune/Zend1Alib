<?php

/**
 * Генерация строки get в запросе.
 * 
 * 
*/
namespace Alib\Builder;
class Get implements \ArrayAccess
{
    protected $_data = array();
    protected $_urlEncode = false;

    protected $_useCurrent = false;
    
    public function __construct($urlencode = true)
    {
        $this->_urlEncode = $urlencode;
    }


    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->_data[] = $value;
        }
        else
        {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }

    /**
     *
     * @return Get
     */
    public function clean()
    {
        $this->_data = array();
        return $this;
    }

    /**
     *
     * @param array $data
     * @return Get
     */
    public function load($data, $elect = null)
    {
        if ($elect !== null and is_array($elect))
        {
            $tempData = array();
            foreach($elect as $value)
            {
                if (isset($data[$value]))
                {
                    $tempData[$value] = $data[$value];
                }
            }
            $data = $tempData;
        }
        $this->_data = $data;
        //\Alib\Test::pr($this->_data);
        return $this;
    }
    
    /**
     *
     * @param type $key
     * @param type $value
     * @return Get
     */
    public function set($key, $value)
    {
        $this->add($key, $value);
        //$this->_data[$key] = $value;
        return $this;
    }

    /**
     * Синоним set
     *
     * @param type $key
     * @param type $value
     * @return Get
     */
    public function add($key, $value)
    {
        if (is_array($key))
        {
            if (count($key) == 1)
            {
                $this->_data[$key[0]] = $value;
            }
            else
            {
                if (!array_key_exists($key[0], $this->_data) or !is_array($this->_data[$key[0]]))
                {
                    $this->_data[$key[0]] = [];
                }
                $this->_data[$key[0]][$key[1]] = $value;
            }
        }
        else
            $this->_data[$key] = $value;
        return $this;
    }


    public function useCurrent($use = true)
    {
        $this->_useCurrent = $use;
        return $this;
    }

    public function getString()
    {
        $res = '';
        $prefix = '?';
        $urlEncode = $this->_urlEncode;
        $data = $this->_data;
        if ($this->_useCurrent)
        {
            $data = $this->_data + $_GET;
        }
        $res = $this->_collectGet($data);
        /*
        foreach ($data as $key => $value)
        {
            if ($urlEncode)
                $value = urlencode($value);
            $res .= $prefix . $key . '=' . $value;
            $prefix = '&';
        }
        */
        return $res;
    }


    protected function _collectGet($data)
    {
        $urlencode = $this->_urlEncode;
        $str = '';
        if ($data and count($data))
        {
            $connect = '?';
            foreach ($data as $key => $value)
            {
                if (is_array($value))
                    $str .= $connect . $this->_collectGetArray($value, $key, $urlencode);
                else if ($urlencode)
                    $str .= $connect . $key . '=' . urlencode($value);
                else
                    $str .= $connect . $key . '=' . $value;
                $connect = '&';
            }
        }
        return $str;
    }

    protected function _collectGetArray($in_value, $name, $urlencode)
    {
        $str = '';
        if (count($in_value))
        {
            $connect = '';
            foreach ($in_value as $key => $value)
            {
                if ($urlencode)
                    $str .= $connect . $name . '[' . $key . ']=' . urlencode($value);
                else
                    $str .= $connect . $name . '[' . $key . ']=' . $value;
                $connect = '&';
            }
        }
        return $str;
    }



    /**
     * Псевдоним add()
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->add($key, $value);
    }

}
