<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 04.10.12
 * Time: 10:18
 *
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Exception;
class RelationToOneRecord extends Record\DataType
{
    const BAD_DATA = 'BAD_DATA';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::BAD_DATA    => "Неверные данные.",
    );


    protected $_recordToRelateName = '';

    /**
     * @var \Alib\Record\AbstractClass\Record
     */
    protected $_recordToRelate = null;

    protected $_recordToRelateKey = 'id';

    public function _processReady()
    {
        $result = true;

        return $result;
    }

    public function setKey($key)
    {
        $this->_recordToRelateKey = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->_recordToRelateKey;
    }


    public function setRecordToRelate($record)
    {
        $this->_recordToRelateName = $record;
        return $this;
    }


    public function getRecordToRelate($id = null)
    {
        if (!$this->_recordToRelateName)
            throw new Exception('Не передан обязательный параметр через setRecord', 1);

        //\Alib\Test::pr($this->_record, 1);
        if ($this->_recordToRelate)
            goto b_e;
        $this->_recordToRelate = $this->_record->getRecord($this->_recordToRelateName);

        b_e:
        if ($id)
            $this->_recordToRelate->retrieve($id);
        return $this->_recordToRelate;
    }

    public function getFormatedValue()
    {
        $record = $this->getRecordToRelate($this->_value);
        return $record->getDataTitle();
        return null;
    }



    public function getList($order = null)
    {
        $record = $this->getRecordToRelate();
        $name = $record->getTableName() . '_getList';
        $result = $record->getModel()->{$name}($order);
        return $result;
    }

}