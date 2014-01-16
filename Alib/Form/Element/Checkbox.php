<?php

namespace Alib\Form\Element;
class Checkbox extends AbstractClass\CheckBox
{
    public function init()
    {
        $this->addFilter('StripTags');
    } 
}
