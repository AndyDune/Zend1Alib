<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dune
 * Date: 22.03.12
 * Time: 8:22
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Db;
class Table
{
    use \Alib\System\Traits\BuildClassName;

    protected $_name = '';
    protected $_nameInGroup = '';
    protected $_simpleSelect = null;

    /**
     * @var Group
     */
    protected $_group;


    /**
     * @var \Alib\Record\AbstractClass\Record
     */
    protected $_record;


    protected $_tableDbObject = null;


    public function __construct($name, Group $group)
    {
        $this->_nameInGroup = $name;
        $this->_group = $group;
        $this->_name =  $group->getTablePrefix() .$name;
    }

    /**
     * Возвращает имя своей таблицы.
     * Важно для создания запроса с участием нескольких таблиц.
     *
     * В системе не используется прямое указание имён таблиц.
     *
     * @return string имя таблицы
     */
    public function getTableName()
    {
        return $this->_name;
    }

    /**
     *
     * @return TableBuildSelect
     */
    public function getForBuildSelect()
    {
        $table = new TableBuildSelect($this);
        return $table;
    }

    /**
     * Возврат объекта группы для этой таблицы.
     *
     * @return Group
     */
    public function getGroup()
    {
        return $this->_group;
    }

    /**
     * @param bool $special
     * @param string $specialTableModule
     * @return TableDb
     */
    public function getTableDbObject($special = false, $specialTableModule = 'Www')
    {
        if (!$this->_tableDbObject)
            $this->_tableDbObject = new TableDb($this->_name);
        return $this->_tableDbObject;
    }


    public function getCols()
    {
        return $this->getTableDbObject()->getCols();
    }


    /**
     * @param string $module
     * @return \Alib\Record\AbstractClass\Record
     */
    public function getRecordObject($module = 'Www')
    {
        $name = $this->_buildNameForRecordClass($this->_nameInGroup, $this->_group->getName(), $module);
        $this->_record = new $name($this->_group->getRealization());
        $this->_record->setTable($this);
        return $this->_record;
    }


    /**
     * @param $name имя таблицы в группе
     * @return SelectSimple
     */
    public function getSelectSimple()
    {
        if (!$this->_simpleSelect)
        {
            $this->_simpleSelect = new SelectSimple($this);
        }
        $this->_simpleSelect->clear();
        return $this->_simpleSelect;
    }


}
