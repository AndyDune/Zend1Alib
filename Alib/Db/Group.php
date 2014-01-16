<?php
/**
 * Created by JetBrains PhpStorm.
 * User: dune
 * Date: 22.03.12
 * Time: 8:17
 */
namespace Alib\Db;
class Group
{
    protected $_prefix = '';
    protected $_name        = '';
    protected $_realization = '';
    protected $_tablePrefix = '';

    protected $_tables = array();

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_db;

    public function __construct($name, $realization = 'base', $prefix = 'rznw')
    {
        $this->_name = $name;
        $this->_realization = $realization;
        $this->_prefix = $prefix;
        $this->_tablePrefix = $prefix . '_' . $name . '_' . $realization . '_';
    }

    /**
     * @return \Zend_Db_Adapter_Abstract
     */
    public function getAdapter()
    {
        return $this->_db;
    }

    /**
     * @param $adapter \Zend_Db_Adapter_Abstract
     * @return Group
     */
    public function setAdapter($adapter)
    {
        $this->_db = $adapter;
        return $this;
    }


    public function getName()
    {
        return $this->_name;
    }

    public function getRealization()
    {
        return $this->_realization;
    }

    /**
     * @param $name имя таблицы в группе
     * @return Table
     */
    public function getTable($name)
    {
        if (!array_key_exists($name, $this->_tables))
        {
            $this->_tables[$name] = new Table($name, $this);
        }
        return $this->_tables[$name];
    }


    public function getTablePrefix()
    {
        return $this->_tablePrefix;
    }

}
