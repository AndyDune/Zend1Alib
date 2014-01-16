<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 09.10.12
 * Time: 14:18
 *
 * Связь для организации дерева в пределах одной записи (таблицы.)
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Exception;
class RelationToSelfTree extends Record\DataType
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

    /**
     * Использовать дополнительно перечисление путей.
     *
     * @var null|string
     */
    protected $_useLine = null;

    protected $_nearbyFields = [];

    public function _processReady()
    {
        $result = true;
        $this->_value = (int)$this->_value;
        if ($this->_value < 0)
            $this->_value = 0;
        return $result;
    }


    public function useLine($field)
    {
        $this->_useLine = $field;
        return $this;
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


    public function getParentList($desc = false)
    {
        if (!$this->_record->getId())
            return [];
        $parentId = $this->_record->{$this->_field};
        $parents = [$this->_record];
        while($parentId != 0)
        {
            $parent = $this->_record->getRecord()->retrieve($parentId);
            $parents[] = $parent;
            $parentId = $parent->{$this->_field};
        }
        if (count($parents) and $desc)
        {
            $parents = array_reverse($parents);
        }
        return $parents;
    }

    public function getRecordToRelate($id = null)
    {
        //\Alib\Test::pr($this->_record, 1);
        if ($this->_recordToRelate)
            goto b_e;
        $this->_recordToRelate = $this->_record->getRecord();

        b_e:

        if ($id)
            $this->_recordToRelate->retrieve($id);
        else if($this->_value)
        {
            $this->_recordToRelate->retrieve($this->_value);
        }
        return $this->_recordToRelate;
    }


    public function getList($orderField = null)
    {
        if ($this->_useLine and !$orderField)
            $orderField = $this->_useLine;
        $record = $this->getRecordToRelate();
        $name = $record->getTableName() . '_getList';
        $result = $record->getModel()->{$name}($orderField);
        return $result;
    }

    public function processAfterSave()
    {
        $return = false;
        if ($this->_useLine)
        {
            $id = $this->_record->getId();

            if ($id)
            {
                $select = $this->_record->getTable()->getSelectSimple();
                $select->addFilterAnd($this->_field, $id, '=', '?i');

                $list = $select->get();

                foreach($list as $value)
                {
                    $record = $this->_record->getRecord()->initFromData($value);
                    $record->getDataObject()->processHavingDataOnly();
                    $record->save([$this->_useLine => '', $this->_field => $id], true);
                }
                //\Alib\Test::pr($list, 1);
            }
            $parents = $this->getParentList(1);
            $line = '';
            foreach($parents as $value)
            {
                $line .= $value->getId() . '/';
            }
            //$line .= $this->_record->getId() . '/';
            $this->_nearbyFields[$this->_useLine] = $line;
        }
        return $return;
    }

    public function processNearbyFields($data)
    {
        foreach($this->_nearbyFields as $key => $value)
        {
            $data[$key] = $value;
        }
        return $data;
    }


}