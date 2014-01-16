<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.09.12
 * Time: 12:19
 *
 * Формирование строки транскрипа килилицы на латиницу.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class StringTranscript extends Record\DataType
{
    protected $_fromField = null;

    public function fromField($field)
    {
        $this->_fromField = $field;
        return $this;
    }

    protected function _processReady()
    {
        if (!$this->_value and $this->_fromField)
            $this->_value = $this->_dataObject[$this->_fromField];
        if ($this->_value)
        {
            $trans = new \Alib\String\Translit($this->_value);
            $this->_value = $trans->make();
        }
        return true;
    }

}
