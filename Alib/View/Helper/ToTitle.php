<?php
/**
 * Отправить выражение в заголовокс страриницы.
 *
 *
 * @author duna
 */

namespace Alib\View\Helper;
use Alib;
class ToTitle extends \Alib\View\HelperAbstract
{
    protected $_data = array();
    public function toTitle($data = null)
    {
        if ($data)
        {
            $accumulator = Alib\Accumulator::getInstance();
            $accumulator->addTitle($data);
            $this->_data[] = $data;
        }
        return $this;
    }

    public function getFromStructure($default = null) // todo убрать этот метод
    {
        die();
        $this->_data = array();
        $reg = Zend_Registry::getInstance();
        $tree = $reg->get('structure_object_tree');
//        print_r($tree);
        if (count($tree))
        {
            foreach($tree as $value)
            {
                $this->_data[] = $value->getTxt();
            }
            
/*            
            $curr = array_pop($tree);
            $this->_data[] = $curr->getTxt();
 */
        }
        else if ($default)
        {
            $this->_data[] = $default;
        }
        return $this;
    }     
    
    public function commit() // todo убрать этот метод
    {
        die();
        $count = count($this->_data);
        $data = $this->_data;
        foreach($data as $val)
        {
            $this->view->headTitle($val);
        }
        return $this;
        while($count > 0)
        {
            $count--;
            $this->view->headTitle($data[$count]);
        }
        return $this;
    }


}