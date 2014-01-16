<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 30.10.12
 * Time: 19:18
 * To change this template use File | Settings | File Templates.
 */

namespace Alib\Record\DataType;
use Alib\Record;
class Int extends Record\DataType
{
    public function _processReady()
    {
        $result = true;
        $this->_value = preg_replace('|-\D|ui', '', $this->_value);
        $this->_value = (int)$this->_value;
        return $result;
    }

}