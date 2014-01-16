<?php


namespace Alib\Form\Element;
class Int extends AbstractClass\Text
{
    public function init()
    {
        $this->addFilter('Int');
    }    
}

