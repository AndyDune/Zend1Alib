<?php
/**
 * Генератор запросов селект для одной таблицы.
 * Предназначен для быстрой работы без программировангия моделей.
 *
 * Версия 1.01
 *
 * История:
 *  1.01 (2012-04-18) Внедрение базового функционала.
 *                    Требует доработки, но использовать уже можно.
 *
 *
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.04.12
 * Time: 11:48
 */
namespace Alib\Db;
class SelectSimple
{
    use Traits\DbAdapter;
    /**
     * @var Table
     */
    protected $_table;
    protected $_tableName;
    protected $_filter = array();
    protected $_order = array();

    protected $_orderRand = false;

    protected $_limitString = '';

    public function __construct(Table $object)
    {
        $this->_table = $object;
        $this->_adapter = $this->_table->getGroup()->getAdapter();
        $this->_tableName = $this->_formatTableName($this->_table->getTableName());
    }

    public function setLimit($limit, $offset = 0)
    {
        $this->_limitString = ' LIMIT ' . (int)$offset .  ', ' . (int)$limit;
        return $this;
    }
    

    protected function _prepareWhere()
    {
        $result = array('sql' => '', 'data' => null);
        foreach($this->_filter as $value)
        {
            switch ($value[0])
            {
                case 'and':
                    if ($result['sql'])
                        $result['sql'] .= ' AND ';
                    $result['sql'] .= $this->_formatFieldName($value[1]) . ' ' . $value[3]
                                   . ' :' . $value[1];
                    $result['data'][$value[1]] = $value[2];
                break;
                case 'and null':
                    if ($result['sql'])
                        $result['sql'] .= ' AND ';
                    $result['sql'] .= $this->_formatFieldName($value[1]) . ' IS NULL';
                break;

                case 'and in':
                    if ($result['sql'])
                        $result['sql'] .= ' AND ';
                    $result['sql'] .= $this->_formatFieldName($value[1]) . ' IN (' . implode(',', $value[2]) . ') ';
                    //die();
                    break;


                case 'and notnull':
                    if ($result['sql'])
                        $result['sql'] .= ' AND ';
                    $result['sql'] .= $this->_formatFieldName($value[1]) . ' IS NOT NULL';
                break;

            }
        }

        if ($result['sql'])
            $result['sql'] = ' WHERE ' . $result['sql'];

        return $result;
    }

    protected function _prepareOrder()
    {
        $result = '';

        if ($this->_orderRand)
        {
            $result = ' ORDER BY RAND()';
            goto befor_return;
        }

        foreach($this->_order as $value)
        {
            if ($result)
                $result .= ', ';
            $result .= $this->_formatFieldName($value[0]) . ' ' . $value[1];
        }
        if ($result)
            $result = ' ORDER BY ' . $result;

        befor_return:
        return $result;

    }

    public function addOrder($field, $desc = false)
    {
        if ($desc)
            $direction = 'DESC';
        else
            $direction = 'ASC';
        $this->_order[] = array($field, $direction);
        return $this;
    }

    /**
     * Включить случайную сортировку выборки.
     *
     * @return SelectSimple
     */
    public function addOrderRand()
    {
        $this->_orderRand = true;
        return $this;
    }




    public function count()
    {
        $sql = 'SELECT COUNT(*) as count FROM ' . $this->_tableName;

        $where = $this->_prepareWhere();

        $sql .= ' ' . $where['sql'];

        //$sql .= ' ' . $this->_prepareOrder();

        //echo $sql, '<br>';
        //die();

        $data = $this->_adapter->fetchOne($sql, $where['data']);

        return $data;
    }

    public function get($limit = null, $shift = 0, $fields = null)
    {
        if ($fields)
        {
            $select_fields = '';
            if (!is_array($fields))
                $fields = array($fields);
            foreach($fields as $value)
            {
                if($select_fields)
                    $select_fields .= ', ';
                $select_fields .= $this->_formatFieldName($value);
            }
        }
        else
        {
            $select_fields = '*';
        }

        $sql = 'SELECT ' . $select_fields . ' FROM ' . $this->_tableName;

        $where = $this->_prepareWhere();

        $sql .= ' ' . $where['sql'];

        $sql .= ' ' . $this->_prepareOrder();

        if ($limit !== null)
            $sql .= ' LIMIT ' . $limit . ' OFFSET ' . $shift;
        else if ($this->_limitString)
            $sql .= $this->_limitString;

        //echo $sql;
        //die();

        $data = $this->_adapter->fetchAll($sql, $where['data']);
        return $data;
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
            $value = (string)$value;
        $data = array('and', $field, $value, $comp);
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

    protected function _formatFieldName($field)
    {
        $field = $this->_adapter->quoteTableAs($field);
        return $field;
    }

    public function clear()
    {
        $this->_filter = [];
        return $this;
    }


}
