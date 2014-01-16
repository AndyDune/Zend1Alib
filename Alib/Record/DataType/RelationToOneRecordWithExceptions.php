<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 15.10.12
 * Time: 16:20
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Exception;
class RelationToOneRecordWithExceptions extends Record\DataType
{
    use \Alib\ArrayClass\Traits\Extract;

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

    public function init()
    {
        $this->setReqValues(['RecordToRelateFieldCheck', '']);
        return $this;
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


    public function getList($order = null)
    {
        $valueToCheck = null;
        // Используем для исключение значение соседнего поля
        if ($fieldForException = $this->getMyFieldToCheck())
        {
            $valueToCheck = $this->_record->getDataObject()->getDataTypeObject($fieldForException)->getValue();
        }
        $record = $this->getRecordToRelate();

        $recordToRelateFieldCheck = $this->getRecordToRelateFieldCheck();
        if ($valueToCheck and $recordToRelateFieldCheck)
        {
            $dataTypeObject = $record->getDataObject()->getDataTypeObject($recordToRelateFieldCheck);
            if ($dataTypeObject instanceof RelationManyToManyWithTree)
            {
                $recordViaRelate = $dataTypeObject->getRecordViaRelate();
                $listWithIds =
                $recordViaRelate->getTable()->getSelectSimple()
                                ->addFilterAnd($dataTypeObject->getRelationTableKeyToRelate(), $valueToCheck, '=', '?i')
                                ->get();
                //\Alib\Test::pr($listWithIds);
                if ($listWithIds and count($listWithIds))
                {
                    $ids = $this->getValuesFromFieldWithArrayList($listWithIds, $dataTypeObject->getRelationTableKeyToMe());
                    //\Alib\Test::pr($ids);
                    $field = $record->getIdFieldName();
                    return $record->getTable()->getSelectSimple()->addFilterIn($field, $ids)->addOrder($field, $order)->get();
                }
            }
        }


        $name = $record->getTableName() . '_getList';
        $result = $record->getModel()->{$name}($order);
        return $result;
    }

    public function getFormatedValue()
    {
        $record = $this->getRecordToRelate($this->_value);
        return $record->getDataTitle();
        return null;
    }


}