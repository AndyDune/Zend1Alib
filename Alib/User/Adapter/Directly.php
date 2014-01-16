<?php

/**
 * 
 */
namespace Alib\User\Adapter;
class Directly extends AbstractClass
{
    public function login()
    {
        $id = $this->_getArgument(1, true);
        $table = $this->_base->getUserDataTableObject();
        return $this->_data = $table->get($id);
    }
}