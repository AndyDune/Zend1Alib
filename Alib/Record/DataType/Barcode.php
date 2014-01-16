<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 02.11.12
 * Time: 15:19
 *
 *
 */

namespace Alib\Record\DataType;
use Alib\Record;
class Barcode extends Record\DataType
{

    const TO_SHORT_CODE = 'TO_SHORT_CODE';
    const TO_LONG_CODE  = 'TO_LONG_CODE';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::TO_SHORT_CODE     => "Слишком короткий штрих-код. Должно быть 13 символов.",
        self::TO_LONG_CODE      => "Слишком длинный штрих-код. Должно быть 13 символов.",
    );


    public function _processReady()
    {
        $result = true;

        $this->_value = preg_replace('|\s\D|ui', '', $this->_value);
        if (strlen($this->_value) < 2)
        {
            $this->_value = null;
        }
        else if (strlen($this->_value) < 13)
        {
            $this->addMessage(self::TO_SHORT_CODE);
            return false;
        }
        else if (strlen($this->_value) > 13)
        {
            $this->addMessage(self::TO_LONG_CODE);
            return false;
        }

        return $result;
    }

    public function getFormatedValue()
    {
        return $this->getValue();
    }


}



