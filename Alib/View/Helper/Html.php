<?php
/**
 * V.02
 * 
 * Контсруктор произвольного тега.
 * 
 * Версии:
 * 2011-09-07 Реализует интерфейс lib\View_Helper_Interface_TableRowDecorator
 * 
 *
 * @package
 * @category
 * @author      Andrey Ryzhov <dune@rznlf.ru>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 */
namespace Alib\View\Helper;
use Alib\Request;
use Alib\View;
class Html extends View\HelperAbstract implements View\Helper\InterfaceClass\TableRowDecorator
{
    protected $_data = '';
    protected $_dataArray = array();
    protected $_value = null;
    protected $_tag = '';
    
    public function html($tag = '', $content = null, $attributes = null)
    {
        // Сброс данных с предыдущего вызова инстанса.
        $this->_data = '';
        $this->_tag = $tag;
        if ($content)
            $this->_data = $this->_attrSimple($tag, $content, $attributes);
        return $this;
    }
    
    protected function _attrSimple($tag, $content, $attributes)
    {
        $attr = '';
        if ($attributes !== null)
        {
            foreach ($attributes as $key => $value)
            {
                $attr .= ' ' . $key. '="' . $value . '"';
            }
        }
        $result = '<' . $tag . $attr;
        if ($content === null)
        {
            $result .= ' />';
        }
        else
        {
            $result .= '>' . $content . '</' . $tag  . '>';
        }
        return $result;
    }

    
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }
    
    public function setData($data)
    {
        $this->_dataArray = $data;
        return $this;
    }

    
    public function get()
    {
        if ($this->_value)
        {
            $this->_data = $this->_attrSimple($this->_tag, $this->_value, array('href' => $this->_value));
        }
        return $this->_data;
    }
    
    public function __toString()
    {
        return $this->_data;
    }

}