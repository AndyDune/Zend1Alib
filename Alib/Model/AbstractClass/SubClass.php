<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 19.04.12
 * Time: 9:55
 *
 *
 */
namespace Alib\Model\AbstractClass;
use Alib\Exception;
abstract class SubClass
{
    use \Alib\Db\Traits\DbAdapter;
    use \Alib\System\Traits\BuildClassName;
    /**
     * Основноек, родительский класс модели
     *
     * @var Base
     */
    protected $_parent;

    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * Имя таблицы в группе с которой есть основная ассоциация.
     *
     * @var string
     */
    protected $_table = null;

    final public function __construct(Base $parent)
    {
        $this->_parent = $parent;
        $this->_adapter = $this->_parent->getGroupAdapter();
        $this->init();
    }

    public function init()
    {

    }

    /**
     * @param null $name
     * @param null $module
     * @return Base
     */
    public function getModel($name = null, $module = null, $type = 'data')
    {
        if (!$name)
            return $this->_parent;

        if (!$module)
        {
            $reg = \Alib\Registry::getInstance();
            $module = $reg->get('module');
        }
        return \Alib\Model\Factory::getModel($name, $module, $type);
    }

    final public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    final public function getTable()
    {
        if (!$this->_table)
        {
            $class = get_class($this);
            $parts = explode('\\', $class);
            $this->_table = $this->_destroyCamelName($parts[count($parts) - 1]);

        }
        return $this->_table;
    }

    /**
     * Выбрать простой список.
     *
     * @param null $order
     * @return array
     */
    public function getList($order = null)
    {
        if (!$order)
            $order = 'id';
        $simpleSelect = $this->_parent->getTableObject($this->getTable())->getSelectSimple();
        if (is_array($order))
        {
            $params = $order;
            if (!isset($params['order_direction']))
                $params['order_direction'] = 0;

            if (!isset($params['shift']))
                $params['shift'] = 0;

            if (isset($params['limit']))
            {
                $simpleSelect->setLimit($params['limit'], $params['shift']);
            }

            if (isset($params['order']))
            {
                $simpleSelect->addOrder($params['order'], $params['order_direction']);
            }

        }
        else if ($order)
        {
            if (is_string($order))
                $simpleSelect->addOrder($order);
        }
        return $simpleSelect->get();
    }

    public function count()
    {
        $simpleSelect = $this->_parent->getTableObject($this->getTable())->getSelectSimple();
        return $simpleSelect->count();
    }


}