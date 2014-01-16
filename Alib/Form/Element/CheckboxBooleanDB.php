<?php

/**
 * 
 * Версии:
 *    2011-08-24 Создан. Проверен. Используется.
 * 
 */

namespace Alib\Form\Element;
class CheckboxBooleanDB extends AbstractClass\Checkbox
{
    
    protected function _init()
    {
        $this->setCheckedValue('y');
        $this->setUncheckedValue('f');
    } 
}
