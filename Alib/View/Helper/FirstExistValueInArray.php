<?php
/**
 * Выводи на печать первое всречное непустое значение из массива ($array).
 * Перебирается входной массив ключей $keys
 *
 * @package
 * @category
 * @author      Andrey Ryzhov <webmaster@rzn.info>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 */
namespace Alib\View\Helper;
use Alib;
class FirstExistValueInArray extends Alib\View\HelperAbstract
{
    private $_data = '';
    private $_keys = [];
    private $_array = [];
    public function direct($array = null, $keys = null, $default = '')
    {
        $this->_data = $default;
        if ($keys !== null)
        {
            if (!is_array($keys))
                $keys = [$keys];
            $this->_keys = $keys;
        }
        if ($array !== null)
        {
            $this->_array = $array;
            $this->_buildData();
        }
        return $this;
    }

    public function get()
    {
        return $this->_data;
    }

    public function setData($data)
    {
        $this->_array = $data;
        return $this;
    }
    
    public function setValue($value)
    {
        $this->_buildData();
        return $this;
    }

    
    protected function _buildData()
    {
        $keys = $this->_keys;
        $array = $this->_array;
        foreach($keys as $key)
        {
            if (isset($array[$key]) and $array[$key])
            {
                $this->_data = $array[$key];
                break;
            }
        }
    }
    

    public function  __toString()
    {
        return $this->_data;
    }
}



