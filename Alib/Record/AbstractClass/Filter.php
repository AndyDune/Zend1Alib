<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.07.12
 * Time: 14:58
 *
 */
namespace Alib\Record\AbstractClass;
abstract class Filter implements \Zend_Filter_Interface
{
    protected $_record = null;
    public function __construct()
    {

    }

    public function setRecord($record)
    {
        $this->_record = $record;
        return $this;
    }
}
