<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 20.09.12
 * Time: 11:53
 *
 * Проверка на существование записи с таким же полем.
 *
 */
namespace Alib\Record\Validate\Db;
class NoRecordExists extends  RecordAbstract
{
    const HAVE_RECORD = 'HAVE_RECORD';

    protected $_messageTemplates = array
    (
        self::HAVE_RECORD => 'Существует запись в которой есть уже такое значение.'

    );

    public function isValid($value)
    {
        $this->_record->retrieveWithField($value, $this->_field);
        $id = $this->_record->getId();
        if ($id and $id != $this->_recordId)
        {
            $this->_error(self::HAVE_RECORD);
            return false;
        }
        return true;
    }
}
