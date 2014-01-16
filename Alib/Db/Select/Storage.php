<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.10.12
 * Time: 10:35
 *
 *
 */
namespace Alib\Db\Select;
use Alib\Db;
class Storage
{
    protected $_selectFields = [];

    protected $_from   = '';
    protected $_string = '';

    protected $_join = '';

    protected $_offset = '';
    protected $_limit = '';

    protected $_orders = [];

    protected $_filter = [];

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    public function __construct()
    {
        $this->_adapter = \Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * @return \Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_adapter;
    }

    /**
     * Поле фром.
     * Пока только одна таблица.
     *
     * @param $table
     * @return Storage
     */
    public function from($table)
    {
        $this->_from = ' FROM ' . $table->getName();
        return $this;
    }

    public function addFieldToSelect($table, $field = null, $params = [])
    {
        if ($table instanceof Db\Table)
            $table = $table->getForBuildSelect();

        $params = $this->_excludeParamsForFieldAdd($params);
        if ($params['all'])
            goto all;
        if (!$field or is_string($field))
        {
            if (!$field or $field == '*')
                $this->_selectFields[] = $table->getName() . '.*';
            else
                $this->_selectFields[] = $table->getName() . '.' . $table->quoteIdentifier($field);
        }
        else if (is_array($field))
        {
            if (false)
            {
                all:
                $field = $table->getTable()->getCols();
            }
            foreach($field as $alias => $field)
            {
                if (is_string($alias))
                    $this->_selectFields[] = $table->getFieldForSelect($field, $alias);
                                     //$table->{$field} . ' as ' . $table->quoteIdentifier($alias);
                else if ($params['prefix'] or $params['postfix'])
                {
                    $alias = $params['prefix'] . $field . $params['postfix'];
                    //$table->setFieldAlias($field, $alias);
                    $this->_selectFields[] = $table->getFieldForSelect($field, $alias);
                        //$table->{$field} . ' as ' . $table->quoteIdentifier($fieldAlias);
                }
                else
                    $this->_selectFields[] = $table->getFieldForSelect($field); //$table->{$field};
            }
        }
        return $this;
    }

    public function addFieldToSelectExclude($table, $fields, $params = [])
    {
        if ($table instanceof Db\Table)
            $table = $table->getForBuildSelect();

        $params = $this->_excludeParamsForFieldAdd($params);
        if (!is_array($fields))
            $fields = [$fields];

        $tableObject = $table->getTable();
        $cols = $tableObject->getCols();

        foreach($cols as $field)
        {
            if (in_array($field, $fields))
                continue;

            if ($params['prefix'] or $params['postfix'])
            {
                $alias = $params['prefix'] . $field . $params['postfix'];
                //$table->setFieldAlias($field, $alias);
                $this->_selectFields[] = $table->getFieldForSelect($field, $alias);
                   // $table->{$field} . ' as ' . $table->quoteIdentifier($params['prefix'] . $field . $params['postfix']);
            }
            else
                $this->_selectFields[] = $table->getFieldForSelect($field); //$table->{$field};
        }
        return $this;
    }

    protected function _excludeParamsForFieldAdd($params)
    {
        if (!isset($params['prefix']))
            $params['prefix'] = '';
        if (!isset($params['postfix']))
            $params['postfix'] = '';
        if (!isset($params['all']))
            $params['all'] = false;

        return $params;
    }


    /**
     * Добавление произвольной строки в запрос.
     * Происходит накопление уже введенных частей запроса со сбросом накопителей.
     *
     * @param $string
     * @return Storage
     */
    public function addJoin($join)
    {
        if ($join instanceof Join)
            $join = $join->get();
        $this->_join .=  ' ' . $join . ' ';
        return $this;
    }

    public function commit()
    {
        $this->_string .= $this->_from
        ;
        $this->_from = '';
        return $this;
    }

    public function addOrder($tableField, $desc = false)
    {
        if ($desc)
            $desc = ' DESC';
        else
            $desc = ' ASC';
        $this->_orders[] = $tableField . $desc;
        return $this;
    }


    public function setLimit($limit, $offset = 0)
    {
        $this->_limit = ' LIMIT ' . $offset .  ', ' . $limit;
        return $this;
    }


    public function getOrderString()
    {
        if (!count($this->_orders))
            return '';
        $prefix = ' ORDER BY ';
        $result = '';
        foreach($this->_orders as $value)
        {
            $result .= $prefix . $value;
            $prefix = ', ';
        }
        return $result;
    }

    public function getWhereString()
    {
        $where = $this->_prepareWhere();
        return $where;
        if (count($this->_filter))
            $where = ' WHERE ';
        $noFirst = false;
        foreach($this->_filter as $value)
        {
            if ($noFirst)
                $where .= ' ' . $value['and_or'];
            $where .= ' ' . $value['string'];
            $noFirst = true;

        }
        return $where;
    }

    protected function _prepareWhere()
    {
        $sql = '';
        foreach($this->_filter as $value)
        {
            switch ($value[0])
            {
                case 'and':
                    if ($sql)
                        $sql .= ' AND ';
                    $sql .= $value[1] . ' ' . $value[3]
                         . ' ' . $value[2];
                    break;
                case 'or':
                    if ($sql)
                        $sql .= ' OR ';
                    $sql .= $value[1] . ' ' . $value[3]
                        . ' ' . $value[2];
                    break;

                case 'and null':
                    if ($sql)
                        $sql .= ' AND ';
                    $sql .= $value[1] . ' IS NULL';
                    break;

                case 'and in':
                    if ($sql)
                        $sql .= ' AND ';
                    $sql .= $value[1] . ' IN (' . implode(',', $value[2]) . ') ';
                    //die();
                    break;


                case 'and notnull':
                    if ($sql)
                        $sql .= ' AND ';
                    $sql .= $value[1] . ' IS NOT NULL';
                    break;

            }
        }

        if ($sql)
            $sql = ' WHERE ' . $sql;

        return $sql;
    }


    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return SelectSimple
     */
    public function addFilterAnd($field, $value, $comp = '=', $state = '?')
    {
        if ($state == '?i')
            $value = (int)$value;
        else
            $value = $this->getAdapter()->quote($value);
        $data = array('and', $field, $value, $comp);
        $this->_filter[] = $data;
        return $this;
    }


    public function addFilterOr($field, $value, $comp = '=', $state = '?')
    {
        if ($state == '?i')
            $value = (int)$value;
        else
            $value = $this->getAdapter()->quote($value);
        $data = array('or', $field, $value, $comp);
        $this->_filter[] = $data;
        return $this;
    }


    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @return SelectSimple
     */
    public function addFilterNullAnd($field)
    {
        $value = null;
        $data = array('and null', $field, null, null);
        $this->_filter[] = $data;
        return $this;
    }

    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @return SelectSimple
     */
    public function addFilterNotNullAnd($field)
    {
        $value = null;
        $data = array('and notnull', $field, null, null);
        $this->_filter[] = $data;
        return $this;
    }

    /**
     * Добавление в фильтра конструкции IN (?)
     *
     *
     * @param string $field
     * @param array $value
     * @return SelectSimple
     */
    public function addFilterIn($field, $value)
    {
        //$field = $this->_formatFieldName($field);

        if (!is_array($value))
            throw new \Alib\Exception ('Должен быть массив', 0);

        $data = array('and in', $field, $value);
        $this->_filter[] = $data;
        return $this;
    }


    public function clearWhere()
    {
        $this->_filter = [];
        return $this;
    }

    public function get()
    {
        $res = ' SELECT ' . implode(', ', $this->_selectFields)
             . $this->_from
             . $this->_join
             . $this->getWhereString()
             . $this->getOrderString()
             . $this->_limit
        ;
        return $res;
    }
}
