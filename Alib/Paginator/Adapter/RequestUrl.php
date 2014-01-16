<?php
/*
 * V.01
 * Вставка номера страницы
 * 
 * Версии:
 * 2011-09-08 Продолжает использоваться.
 * 
 */
namespace Alib\Paginator\Adapter;
use Alib\Request;
class RequestUrl extends AbstractClass
{
    /**
     *
     * @var Dlib_Request
     */
    protected $_object;

    protected $_numberKeyName = 2;
    protected $_numberValueName = 3;

    public function __construct()
    {
        $object = Request::getInstance();
        $this->_object = $object;
        $count = count($object);
        $name_position = $count + 1;
        $value_position = $count + 2;

        if ($count > 1)
        {
            for($current = 2; $current <= $count; $current++)
            {
                $next = $current + 1;
//                echo $object[$current];
//                echo $object[$next];
                if ($object[$current] == $this->_key and $object[$next] and (int)$object[$next] > 0 and !isset($object[$next + 1]))
                {
                    $name_position = $current;
                    $value_position = $next;
                }
            }
        }
        $this->_numberKeyName = $name_position;
        $this->_numberValueName = $value_position;
    }
    
    public function get()
    {
        $this->_object->setCommand($this->_key, $this->_numberKeyName);
        $this->_object->setCommand($this->_number, $this->_numberValueName);
//        $this->_object->setGet($this->_key, $this->_number);
        return $this->_object->getUrl();
    }
}


