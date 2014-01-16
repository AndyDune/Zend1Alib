<?php

namespace rzn\lib\www;
//class Form_Decorator_StringDateTime extends Form_Decorator_Composite
class Form_Decorator_StringDateTime extends \Zend_Form_Decorator_Form
{
 
    public function buildInput()
    {
        $element = $this->getElement();
        $value = $element->getValue();
        if (!$value)
            $value = date('d.m.Y H:i');
        else
        {
            $dateBirth = new \Zend_Date($value, 'yyyy-MM-dd HH:mm:ss');
            $value = $dateBirth->toString('dd.MM.yyyy HH:mm');
        }
        
        $helper  = $element->helper;
        return $element->getView()->$helper(
            $element->getName(),
            $value,
            $element->getAttribs(),
            $element->options
        );
    }
    
    
    
}
