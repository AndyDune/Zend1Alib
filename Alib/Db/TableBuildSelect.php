<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 23.04.12
 * Time: 18:00
 *
 * Участие таблицы в мультитабличных зпросах.
 * Заковычиваение имени таблиц и полей.
 *
 */
namespace Alib\Db;
class TableBuildSelect extends TableInSelect
{
    /**
     * @var Table
     */
    protected $_table;

    protected $_noFirstCallFieldForSelect = false;

    public function __construct(Table $table, $alias = null)
    {
        $this->_table = $table;
        $this->_adapter = $this->_table->getGroup()->getAdapter();
        if ($alias)
            $this->_tableName = $alias;
        else
            $this->_tableName = $this->_formatTableName($this->_table->getTableName());
    }


    /**
     * @return Table
     */
    public function getTable()
    {
        return $this->_table;
    }

    /*
    public function getFieldForSelect($name, $alias = null)
    {
        $result = $this->_tableName . '.' . $this->_formatTableName($name);
        if ($alias)
            $result .= ' AS ' . $this->_formatTableName($alias);

        return $result;
    }
     */



}
