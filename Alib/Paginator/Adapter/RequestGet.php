<?php
/* 
 * Вставка номера страницы
 */

namespace Alib\Paginator\Adapter;
use Alib\Request;
class RequestGet extends AbstractClass
{
    protected $_object;
    public function __construct()
    {
        $this->_object = Request::getInstance();
    }
    public function get()
    {
        $this->_object->setGet($this->_key, $this->_number);
        return $this->_object->getUrl();
    }
}

