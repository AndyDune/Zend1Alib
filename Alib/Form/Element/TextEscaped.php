<?php


namespace Alib\Form\Element;
class TextEscaped extends AbstractClass\Textarea
{
    
    public function init()
    {
        $this->addFilter('StripTags');
    } 
}