<?php

/**
 * V.08
 * Специальный класс для выборки списка 
 *
 * История:
 * 
 * 2011-09-08 Доработан метод setOrder() возвращает содержащий объект.
 * 2011-08-30 При выборке списка можно указывать какие поля включать в выборку. 3-й аргумент в методе get()
 * 2011-08-25 Новый метод 
 * 2011-07-26 Новый метод getTableObject(). Возврат объекта таблицы.
 * 2011-06-15 Добавлен метод addFilterNullAnd() - Поля с NULL
 * 2011-04-18 Добавлен метод getTableOfGroup() - возврат объекта таблицы в группе.
 * 2011-04-11 Выделен как новый абстрактный
 *
 *
 */
namespace Alib\Db\First\Abs;
use Alib;
abstract class Select implements \Countable
{
    
    /**
     *
     * @var rzn\model\db\abs\www\Data
     */
    protected $_tableObject = null;

    /**
     * Адапрет к базе данных
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    protected $_filter = array();

    protected $_order = '';

    protected $_tableObjectName = '';

    protected $_tableAlias = '';


    public function __construct(Alib\Db\First\Abs\Table $table_object)
    {
        $this->_tableObject = $table_object;
        $this->_db = $this->_tableObject->getAdapter();
        $this->_tableObjectName = $this->_tableObject->getTableName();

    }

    
    /**
     * Вернуть объект таблицы
     *
     * @return \rzn\model\db\abs\www\Data
     */
    public function getTableObject()
    {
        return $this->_tableObject;
    }
    
    
    /**
     * Возвратить таблицу из группы.
     *
     * @param string $name имя таблицы в группе
     * @return \rzn\model\db\abs\www\Data
     */
    public function getTableOfGroup($name)
    {
        $obj = Alib\Db\Factory::table($name, $this->_tableObject);
        return $obj;
    }

    /**
     * Возвратить объект-выборщик из группы.
     *
     * @param string $name имя таблицы в группе
     * @return \rzn\model\db\abs\www\Select
     */
    public function getSelectOfGroup($name)
    {
        $obj = Alib\Db\Factory::select($name, $this->_tableObject);
        return $obj;
    }
    
    
    /**
     * Возвратить тия таблицы из группы.
     *
     * @param string $name имя таблицы в группе
     * @return \rzn\model\db\abs\www\Data
     */
    public function getTableNameOfGroup($name)
    {
        $obj = Alib\Db\Factory::table($name, $this->_tableObject);
        return $obj->getTableName();
    }


    public function clearFilter()
    {
        $this->_filter = array();
        return $this;
    }

    public function clear()
    {
        $this->_filter = array();
        $this->_order = '';
        return $this;
    }
    
    /**
     * Запускается сразу после вызова объеката из фабрики.
     * 
     */
    public function initFactory()
    {
        
    }
    

    public function setOrder($string)
    {
        $this->_order = $string;
        return $this;
    }

    public function count()
    {
        $select = $this->_tableObject->select();
        if ($this->_tableAlias)
            $select->from(array($this->_tableAlias => $this->_tableObject), array('COUNT(id) as count'));
        else
            $select->from($this->_tableObject, array('COUNT(id) as count'));

        $select = $this->_collectWhere($select);
//        echo $select; die();
        $data = $this->_tableObject->fetchRow($select);
        if ($data === null)
            return 0;
        return $data->count;
    }

    public function get($limit = null, $shift = 0, $fields = null)
    {
        if ($fields)
        {
            if (!is_array($fields))
                $fields = array($fields);
            $select_fields = $fields;
        }
        else
        {
            $select_fields = array('*');
        }
        $select = $this->_tableObject->select();
        if ($this->_tableAlias)
            $select->from(array($this->_tableAlias => $this->_tableObject), $select_fields);
        else
            $select->from($this->_tableObject, $select_fields);

        $select = $this->_collectWhere($select);

        if ($this->_order)
            $select->order($this->_order);

        if ($limit !== null)
            $select->limit($limit, $shift);
//        echo $select;die();
        $data = $this->_tableObject->fetchAll($select);
        return $data;
    }

    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return System_Catalog_List
     */
    public function addFilterAnd($field, $value, $comp = '=', $state = '?')
    {
        $field = $this->_formatFieldName($field);

        if ($state == '?i')
            $value = (int)$value;
        else
            $value = (string)$value;
        $data = array('where', $field . ' '. $comp . ' ' . $state, $value);
        $this->_filter[] = $data;
        //$this->_select->where($field . ' '. $comp . ' ' . $state , $value);
        return $this;
    }

    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return System_Catalog_List
     */
    public function addFilterNullAnd($field)
    {
        $field = $this->_formatFieldName($field);

        $value = null;
        $data = array('where', $field . ' IS NULL', null);
        $this->_filter[] = $data;
        //$this->_select->where($field . ' '. $comp . ' ' . $state , $value);
        return $this;
    }
    

    /**
     * Добавление в фильтра конструкции IN (?)
     *
     *
     * @param string $field
     * @param array $value
     * @return Model_Db_Table_Common_Abstract_List
     */
    public function addFilterIn($field, $value)
    {
        $field = $this->_formatFieldName($field);

        if (!is_array($value))
            throw new Alib\Exception ('Должен быть массив', 0);

        $data = array('where', $field . ' IN (?)', $value);
        $this->_filter[] = $data;
        //$this->_select->where($field . ' '. $comp . ' ' . $state , $value);
        return $this;
    }


    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return System_Catalog_List
     */
    public function addFilterBitween($field, $value1, $value2)
    {
        $state = null;
        $comp = null;
        $select = $this->_tableObject->select();
        $field = $this->_formatFieldName($field);

        if ($state == '?i')
            $value = (int)$value1;
        else
            $value = (string)$value1;

        $select->where($field . ' '. $comp . ' ' . $state , $value);
        return $this;
    }

    protected function _formatFieldName($field)
    {
        $field = $this->_db->quoteTableAs($field);
        if ($this->_tableAlias)
              $field = $this->_tableAlias . '.' . $field;
        return $field;
    }

    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return System_Catalog_List
     */
    public function addFilterOr($field, $value, $state = '?', $comp = '=')
    {
        $field = $this->_formatFieldName($field);
        $data = array('orWhere', $field . ' '. $comp . ' ' . $state, $value);
        $this->_filter[] = $data;
        return $this;
    }

    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return System_Catalog_List
     */
    public function addFilterAndILike($field, $value)
    {

        $field = $this->_formatFieldName($field);
        $data = array('where', $field . " ILIKE ?", '%' . $value . '%');
        //$data = array('where', $field, $value);
        $this->_filter[] = $data;
        return $this;
    }



    protected function _collectWhere($select)
    {
        $data = $this->_filter;
        foreach ($data as $value)
        {
            switch ($value[0])
            {
                case 'where':
                case 'orWhere':
                    $select->$value[0]($value[1], $value[2]);
                break;
            }
        }
        return $select;
    }
    

}


