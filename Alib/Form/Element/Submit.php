<?php


namespace Alib\Form\Element;
class Submit extends \Zend_Form_Element_Submit
{
    
    /**
     * Необходимо для ссохранения состояний
     *
     * @return array
     */
    public function setMessages($array = null)
    {
        $this->_messages = $array;
        return $this;
    }    
    
    
}

