<?php
/* 
 * Абстрактный класс для пагинации.
 */
namespace Alib\Paginator\Adapter;
class AbstractClass
{
    protected $_number = 1;
    protected $_key = 'page';
    public function setNumber($value)
    {
        $this->_number = $value;
    }
    
    public function setPageKey($value = 'page')
    {
        $this->_key = $value;
    }
    public function get()
    {
        return '';
    }

}

