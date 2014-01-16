<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 27.08.12
 * Time: 14:35
 *
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class Money extends Record\DataType
{

    public function _processReady()
    {
        $result = true;

        $this->_value = str_replace(',', '.', $this->_value);
        $this->_value = preg_replace('|[^.0-9]|ui', '', $this->_value);

        $parts = explode('.', $this->_value);
        if (count($parts) < 2)
            $parts = explode(',', $this->_value);

        if (!isset($parts[1]))
            $parts[1] = '00';
        else
            $parts[1] = substr((int)$parts[1], 0, 2);

        $parts[0] = (int)$parts[0];

        $this->_value = $parts[0] . '.' . $parts[1];

        return $result;
    }

}

