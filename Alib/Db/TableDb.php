<?php
/**
 * User: dune
 * Date: 22.03.12
 * Time: 8:37
 */
namespace Alib\Db;
class TableDb  extends \Zend_Db_Table
{

    protected $_id = null;

    protected $_name = '';

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


    public function __construct($name)
    {
        $this->_name = $name;
        parent::__construct();
    }


    /**
     * Установка идентификатора записи
     *
     * @param iteger $id идентификатор записи
     * @return TableDb
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
     * @return TableDb
     */
    public function updateWithId($data, $id = null)
    {
        $update = $this->_prepareData($data);
        if (!$update)
            return false;

        $id = $this->_checkId($id);

        $where = $this->getAdapter()->quoteInto($this->_formatFieldName($this->_primaryKey) . ' = ?', $id);
        return $this->update($update, $where);
    }

    /**
     * Обновление данных для записи с укзанным id.
     *
     * id можно указать через useId($id)
     *
     * @param <type> $data
     * @param <type> $id
     * @return TableDb
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
     * @return TableDb
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

    public function getCols()
    {
        return $this->_getCols();
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
