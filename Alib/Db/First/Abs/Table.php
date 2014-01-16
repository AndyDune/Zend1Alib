<?php

/**
 * Общий класс работой с таблицами системы.
 *
 * Версии:
 * 2011-07-11 Добавлен метод getSelect() - выборка объекта для составления запросов к базе
 *            Добавлен метод getTableObjectOfGroup() - выборка объекта таблицы в БД из группы.
 * 2011-04-11 Экспериментальная версия
 *
 */
namespace Alib\Db\First\Abs;
use Alib;
abstract class Table extends \Zend_Db_Table
{
    protected $_id = null;

    protected $_name = '';

    protected $_prefix = 'Alib';
    protected $_group = null;
    protected $_nameData = null;
    protected $_realization = 'base';

    protected $_dataKey = '';

    /**
     * Имя первичного ключа. Проставить важно.
     * Если не известно изначально то поставить пустую строку.
     *
     * @var string
     */
    protected $_primaryKey = '';

    /**
     *
     *
     * @var boolean
     */
    protected $_sequence = true;


    public function __construct($name, $group, $realization = 'base', $prefix = 'rznw')
    {
        // Сохраняются данные для передачи другому классу к группе
        $this->_prefix = $prefix;
        $this->_group = $group;
        $this->_nameData = $name;
        $this->_realization = $realization;

        $this->_name = $prefix
                     . '_'
                     . $group
                     . '_'
                     . $realization
                     . '_'
                     . $name;
         parent::__construct();
    }

    
    /**
     * Запускается сразу после вызова объеката из фабрики.
     * 
     */
    public function initFactory()
    {
        
    }
    
    
    public function getSelect()
    {
        return Alib\Db\Factory::select($this->_nameData, $this->_group, $this->_realization, $this->_prefix);
    }

    /**
     * Выборка объекта таблицы в БД из группы.
     * 
     * @param string $name
     * @return Data 
     */
    public function getTableObjectOfGroup($name)
    {
        return Alib\Db\Factory::table($name, $this->_group, $this->_realization, $this->_prefix);
    }
    
    

    public function getRealization()
    {
        return $this->_realization;
    }

    public function getGroup()
    {
        return $this->_group;
    }
    
    public function getPrifix()
    {
        return $this->_prefix;
    }

    public function  clear()
    {

    }

     /**
     * Установка идентификатора записи
     *
     * @param iteger $id идентификатор записи
     * @return Model_Db_Table_Common_Abstract_Data
     */
    public function useId($id)
    {
        $this->_id = $id;
        return $this;
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
     * Вставска данных.
     * Может удалять первычный ключ из массива.
     *
     * id можно указать через useId($id)
     *
     * @param array $data
     * @param boolean $no_id флаг удаления первычного ключа из сохраняемых данных.
     * @return integer
     */
    public function insert(array $data, $no_id = false)
    {
        $update = $this->_prepareData($data);
        if (!$update)
            return false;
        if ($no_id and isset($update[$this->_primaryKey]))
            unset($update[$this->_primaryKey]);

        return parent::insert($update);
    }

    /**
     */
    public function get($id = null)
    {
        $id = $this->_checkId($id);
        $where = $this->getAdapter()->quoteInto($this->_formatFieldName($this->_primaryKey) . ' = ?', $id);
        $data = $this->fetchRow($where);
        return $data;
    }

    /**
     * Возвратить ряд с ключем-строкой.
     * Это интересно при выборке по Логину, электроадресу, урлу.
     */
    public function getWithStringField($value, $field)
    {
        $where = $this->getAdapter()->quoteInto($this->_formatFieldName($field) . ' = ?', (string)$value);
        $data = $this->fetchRow($where);
        return $data;
    }


    /**
     * Обновление данных для записи с укзанным id.
     *
     * id можно указать через useId($id)
     *
     * @param <type> $data
     * @param <type> $id
     * @return Model_Db_Table_News_Data
     */
    public function updateWithId($data, $id = null)
    {
        $update = $this->_prepareData($data);
        if (!$update)
            return false;

        $id = $this->_checkId($id);

        $where = $this->getAdapter()->quoteInto($this->_formatFieldName($this->_primaryKey) . ' = ?', $id);
        $this->update($update, $where);
        return $this;
    }

    /**
     * Обновление данных для записи с укзанным id.
     *
     * id можно указать через useId($id)
     *
     * @param <type> $data
     * @param <type> $id
     * @return Model_Db_Table_News_Data
     */
    public function updateWithField($data, $id = null, $field = 'id')
    {
        $update = $this->_prepareData($data);
        if (!$update)
            return false;

        $id = $this->_checkId($id);

        $where = $this->getAdapter()->quoteInto($this->_formatFieldName($field) . ' = ?', $id);
        $this->update($update, $where);
        return $this;
    }
    

    /**
     * Удалить запись с идентификатором.
     * Удаляются такжа даные из таблиц со связями.
     *
     * @param integer $id
     * @return Model_Db_Table_Common_Abstract_Data
     */
    public function deleteWithId($id = null)
    {
        $id = $this->_checkId($id);

        // Удаление самой записи
        $where = $this->getAdapter()->quoteInto($this->_formatFieldName($this->_primaryKey) . ' = ?', $id);
        $this->delete($where);
        return $this;
    }


    /**
     * Проверка id
     *
     * @param integer $id
     * @return integer
     */
    protected function _checkId($id)
    {
        if ($id === null)
        {
            if ($this->_id === null)
            {
                    echo 'Необходимо указать id записи'; die(); // В дальнейшем заменить на исключение
            }
            $id = $this->_id;
        }
        return $id;
    }


    /**
     * Подготовка данных для вставки.
     * Использует метаданные таблицы.
     *
     * @param array $data
     * @return array
     */
    protected function _prepareData($data)
    {
        $info = $this->info();
        $cols = $info['cols'];

        $update = array();
        if (!is_array($data))
        {
            return false;
        }

        foreach ($data as $column => $value)
        {
            if (in_array($column, $cols))
            {
                $update[$column] = $value;
            }
        }
        return $update;
    }

    /**
     * Установка $this->_primaryKey из $this->_primary если $this->_primaryKey == false
     */
    public function init()
    {
        if (!$this->_primaryKey)
        {
            $info = $this->info();
            if (isset($info['primary'][1]))
            {
                $this->_primaryKey = $info['primary'][1];
            }
        }

    }

    protected function _formatFieldName($field)
    {
        $field = $this->getAdapter()->quoteTableAs($field);
        return $field;
    }

}

