<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 27.08.12
 * Time: 14:33
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class Boolean extends Record\DataType
{
    public function _processReady()
    {
        $result = true;

        if ((boolean)$this->_value)
            $this->_value = 1;
        else
            $this->_value = 0;

        return $result;
    }

    public function getFormatedValue()
    {
        $value = $this->getValue();
        if ($value)
            return 'Да';
        return 'Нет';
    }

}
