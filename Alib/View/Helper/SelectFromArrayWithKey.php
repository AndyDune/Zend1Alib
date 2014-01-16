<?php
/**
 * V.01
 * 
 * Выбрать значение по ключу из массива.
 *
 * Версии:
 *   2011-09-21 Создание. Применение.
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
use Alib\Registry;
class SelectFromArrayWithKey extends \Alib\View\HelperAbstract

{
    private $_data = '';
    private $_key = array();
    private $_array = array();


    public function direct($array = null, $key = null, $default = '')
    {
        $this->_data = $default;
        if ($key !== null)
            $this->_key = $key;
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
        if (array_key_exists($this->_key, $this->_array))
              $this->_data = $this->_array[$this->_key];
    }
    

    public function  __toString()
    {
        return $this->_data;
    }
}