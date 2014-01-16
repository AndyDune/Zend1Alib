<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 20.11.12
 * Time: 18:28
 *
 * Ограниченный набор возможных значений.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class Set extends Record\DataType
{

    const WRONG_SET_VALUE = 'WRONG_SET_VALUE';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::WRONG_SET_VALUE    => "Недопустимое значение.",
    );

    protected $_validSetValues = [];

    public function setValid($array)
    {
        $this->_validSetValues = $array;
        return $this;
    }

    public function _processReady()
    {
        $result = true;
        if (!in_array($this->_value, $this->_validSetValues))
        {
            $this->addMessage(self::WRONG_SET_VALUE);
            $result = false;
        }
        return $result;
    }

}
