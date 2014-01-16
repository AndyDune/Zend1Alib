<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 12.10.12
 * Time: 12:01
 *
 *
 *
 */

namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Exception;
class RelationManyToManyWithTree extends Record\DataType
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

    protected $_recordViaRelateName = '';

    /**
     * @var \Alib\Record\AbstractClass\Record
     */
    protected $_recordViaRelate = null;

    protected $_relationTableKeyToMe = '';
    protected $_relationTableKeyToRelate = '';

    protected $_IdsToSave = [];
    protected $_dataViaRelate = [];

    public function _processReady()
    {
        $result = true;
        $this->_IdsToSave = $this->_value;
        $this->_dataViaRelate = $this->getDataRelateVia();
        //\Alib\Test::pr($this->_value, 1);
        //if (is_array($this->_value))
        $this->_value = 0;
        return $result;
    }

    public function setRelationTableKeyToRelate($key)
    {
        $this->_relationTableKeyToRelate = $key;
        return $this;
    }

    public function getRelationTableKeyToRelate()
    {
        return $this->_relationTableKeyToRelate;
    }


    public function setRelationTableKeyToMe($key)
    {
        $this->_relationTableKeyToMe = $key;
        return $this;
    }

    public function getRelationTableKeyToMe()
    {
        return $this->_relationTableKeyToMe;
    }


    public function setRecordToRelate($record)
    {
        $this->_recordToRelateName = $record;
        return $this;
    }


    public function setRecordViaRelate($name)
    {
        $this->_recordViaRelateName = $name;
        return $this;
    }

    public function getRecordViaRelate()
    {
        if (!$this->_recordViaRelateName)
            throw new Exception('Не передан обязательный параметр через setTableViaRelate', 1);

        //\Alib\Test::pr($this->_record, 1);
        if ($this->_recordViaRelate)
            goto b_e;
        $this->_recordViaRelate = $this->_record->getRecord($this->_recordViaRelateName);

        b_e:
        return $this->_recordViaRelate;
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
        $record = $this->getRecordToRelate();
        if ($record->getDbFieldMetaData('parent_line') and !$order)
            $order = 'parent_line';
        $name = $record->getTableName() . '_getList';
        $result = $record->getModel()->{$name}($order);
        return $result;
    }

    public function getIdsRelateTo()
    {
        $list = $this->getDataRelateVia();
        if (!$list)
            return null;

        $result = [];
        foreach($list as $value)
        {
            $result[] = $value[$this->_relationTableKeyToRelate];
        }
        return $result;
    }

    public function getDataRelateVia()
    {
        $id = $this->_record->getId();
        if (!$id)
            return null;
        $select = $this->getRecordViaRelate()->getTable()->getSelectSimple();
        $select->addFilterAnd($this->_relationTableKeyToMe, $id);
        $list = $select->get();
        if (!$list or !count($list))
            return null;
        return $list;
    }



    public function processAfterSave()
    {
        $return = false;

        $relId = $this->getRecordViaRelate()->getIdFieldName();

        /**
         * id которые есть в системе
         */
        $idsRelateToHad = [];


        /**
         * Поле в таблице связи для соединением со связываемой запсиью.
         */
        $relationTableKeyToRelate = $this->getRelationTableKeyToRelate();

        /**
         * Поле в таблице связи для соединением со связываемой запсиью.
         */
        $relationTableKeyToMe = $this->getRelationTableKeyToMe();


//        \Alib\Test::pr($this->_dataViaRelate);

        if ($this->_dataViaRelate)
        {
            foreach($this->_dataViaRelate as $dataViaRelate)
            {
                if (!$this->_IdsToSave or !in_array($dataViaRelate[$relationTableKeyToRelate], $this->_IdsToSave))
                {
                    $this->getRecordViaRelate()->retrieve($dataViaRelate[$relId])->delete();
                }
                else
                {
                    $idsRelateToHad[] = $dataViaRelate[$relationTableKeyToRelate];
                }
            }
        }
        //echo $relationTableKeyToRelate;
        //\Alib\Test::pr($this->_IdsToSave);
        if ($this->_IdsToSave and count($this->_IdsToSave))
        {
            $idMy = $this->_record->getId();
            $recordVia = $this->getRecordViaRelate();
            foreach($this->_IdsToSave as $id)
            {
                if ($id and !in_array($id, $idsRelateToHad))
                {
                    $array = [$relationTableKeyToMe => $idMy, $relationTableKeyToRelate => $id];
                    $recordVia->clear()->save($array);
                }
            }
            $this->_value = count($this->_IdsToSave);
            $return = true;
        }

        return $return;
    }



}
