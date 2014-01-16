<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 28.08.12
 * Time: 10:11
 *
 * Упрощенный класс для помощи в герерации запросов.
 *
 */
namespace Alib\Db;
class TableInSelect
{

    protected $_tableName = '';

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    protected $_noFirstCallFieldForSelect = false;

    protected $_fieldAlias = [];

    public function __construct($tableName, $adapter)
    {
        $this->_adapter = $adapter;
        $this->_tableName = $this->_formatTableName($tableName);

    }

    protected function _formatTableName($table)
    {
        $table = $this->_adapter->quoteTableAs($table);
        return $table;
    }

    public function quoteIdentifier($ident, $auto=false)
    {
        $res = $this->_adapter->quoteIdentifier($ident, $auto);
        return $res;
    }

    public function setFieldAlias($field, $alias = null)
    {
        if (is_array($field))
        {
            $this->_fieldAlias = $field;
        }
        else
            $this->_fieldAlias[$field] = $alias;
        return $this;
    }



    /**
     * Окружить пробелами.
     *
     * @param $value
     * @return string
     */
    public function withSpaces($value)
    {
        return ' ' . $value . ' ';
    }

    /**
     * Нарисовать равенство со значением.
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @param $fieldMy
     * @param $value
     * @param $type
     * null = string
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal

     *
     * @return string
     */
    public function equalToValue($fieldMy, $value, $type = null)
    {
        $str = ' ' . $this->{$fieldMy}
            . ' = '
            . $this->_adapter->quote($value, $type);

        return $str;
    }

    /**
     * Выборка данных при ассоц. с данными из массива.
     *
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @param $fieldMy
     * @param $value
     * @param $type
     * null = string
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal

     *
     * @return TableBuildSelect
     */
    public function in($fieldMy, $array, $type = null)
    {
        foreach($array as $key => $value)
        {
            $array[$key] = $this->_adapter->quote($value, $type);
        }
        $str = ' ' . $this->{$fieldMy}
            . ' IN ('
            . implode($array, ',')
            . ' ) ';

        return $str;
    }



    /**
     * Сравнение с NULL
     *
     * @param $fieldMy
     * @param string $operator
     * @return string
     */
    public function operationWithNull($fieldMy, $operator = '=')
    {
        $str = ' ' . $this->{$fieldMy}
            . ' '
            . $operator
            . ' NULL '
        ;

        return $str;
    }


    /**
     * Сравнение с полем таблицы, даже другой.
     *
     * @param $fieldMy
     * @param $fieldEnemy
     * @return string
     */
    public function equalToField($fieldMy, $fieldEnemy)
    {
        $str = ' ' . $this->{$fieldMy}
            . ' = '
            . $fieldEnemy;
        return $str;
    }


    public function joinOn($otherTable, $fieldThis, $otherTableField)
    {
        $string =  ' JOIN  '
            . $otherTable->getName() . ' ON ' . $this->{$fieldThis} . ' = ' . $otherTable->{$otherTableField};

        return $string;
    }

    public function joinLeftOn($otherTable, $fieldThis, $otherTableField)
    {
        $string =  ' LEFT JOIN  '
            . $otherTable->getName() . ' ON ' . $this->{$fieldThis} . ' = ' . $otherTable->{$otherTableField};

        return $string;
    }



    public function getName()
    {
        return $this->_tableName;
    }

    public function getFieldForSelect($name, $alias = null)
    {
        $result = $this->_tableName . '.' . $this->_formatTableName($name);
        if ($alias)
            $result .= ' AS ' . $this->_formatTableName($alias);
        else if (array_key_exists($name, $this->_fieldAlias))
            $result .= ' AS ' . $this->_formatTableName($this->_fieldAlias[$name]);
        return $result;
    }

    //public function get

    public function getNameNextInFrom()
    {
        return ', ' . $this->_tableName;
    }

    public function __get($string)
    {
        return $this->_tableName . '.' . $this->_formatTableName($string);
    }

}

