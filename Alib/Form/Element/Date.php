<?php


namespace Alib\Form\Element;
class Date extends AbstractClass\Text
{
    public function _init()
    {
        
//        $decorator = new Form_Decorator_StringDateTime();
//        $this->setDecorators(array($decorator));

//        $decs = $this->getDecorators();
//        print_r($decs);
        
        
        $filter = new \Alib\Filter\DateTime();
        $this->addFilter($filter);
        
    }    
}
