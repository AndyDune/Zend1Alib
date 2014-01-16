<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.04.12
 * Time: 14:53
 */
namespace Alib\Model\AbstractClass;
use Alib\Exception;
abstract class Base
{

    /**
     * Имя текущего моделя.
     * Текущий - это модуль в котором находится модель.
     *
     * @var string
     */
    protected $_moduleName = '';

    protected $_groups = array();
    protected $_groupsObjects = array();
    protected $_groupsRealizations = array();

    /**
     * Массив отношениий методок, которых нет в основном классе субклассам.
     * Запонять в дочерних классах.
     *
     * @var array
     */
    protected $_methodsToSubObjects = array();


    /**
     * Созданные объекты субклассов.
     * @var array
     */
    protected $_subObjects = array();

    /**
     * Следующим методом __call кешировать результат.
     *
     * @var bool
     */
    protected $_cacheNext     = false;


    /**
     * Тип кеширования
     *
     * @var string
     */
    protected $_cacheType = '';

    /**
     * Не актуально !!
     *
     * Следующим методом __call кешировать результат.
     * Специальное кеширование статистической атоамрной информации.
     * Возможно в дальнейшем мемкеш.
     *
     * @var bool
     */
    protected $_cacheAtomNext = false;


    /**
     *
     * @var \Alib\EventManager
     */
    protected $_events = null;


    public function __construct($realization = null)
    {
        foreach($this->_groups as $value)
        {
            if (is_string($realization))
                $this->_groupsRealizations[$value] = $realization;
            else
            {
                $this->_groupsRealizations[$value] = 'base';
            }
        }
        $this->init();
        $class = get_class($this);
        $parts = explode('\\', $class);
        $this->_moduleName = $parts[1];
    }


    public function setEventManager($events)
    {
        $this->_events = $events;
        return $this;
    }

    /**
     * @return \Alib\EventManager
     * @throws \Alib\Exception
     */
    public function events()
    {
        if (!$this->_events)
        {
            throw new \Alib\Exception('Не установлен менеджер событий', 1);
        }
        return $this->_events;
    }



    /**
     * Включение кеширования результатов работы следующего метода.
     * Работает с атомарной информацией.
     *
     * @return Base
     */
    public function cacheAtomNext($flag = true)
    {
        $this->_cacheType = 'modelAtom';
        $this->_cacheNext = $flag;
        return $this;
    }

    /**
     * Включение кеширования результатов работы следующего метода.
     * Работает с очень разнообразной информацией.
     *
     * @return Base
     */
    public function cacheNext($flag = true)
    {
        $this->_cacheType = 'model';
        $this->_cacheNext = $flag;
        return $this;
    }

    public function isCacheNext()
    {
            return $this->_cacheNext;
    }

    public function getCacheNextType()
    {
        return $this->_cacheType;
    }


    public function __call($name, $params = null)
    {
        $this->events()->trigger('model.call.method', $this, ['method' => $name, 'params' => $params]);
        if ($this->_cacheNext)
        {
            $class = str_replace('\\', '_', get_class($this));
            $key = $class . '_' . md5($name . '+' . serialize($params));
            $cache = \Alib\Cache::factory($this->_cacheType);
            $result = $cache->load($key);
            if ($result or is_array($result))
                goto before_return;
        }

        $parts = explode('_', $name, 2);
        if (count($parts) > 1)
        {
            $object = $this->_getSubObject($parts[0]);
            $name = $parts[1];
            goto before_method_call;
        }
        if (!array_key_exists($name, $this->_methodsToSubObjects))
            throw new Exception('Вызывается несуществующий метод либо нет описания передачи метода субобъекту.', 1);
        $object = $this->_getSubObject($this->_methodsToSubObjects[$name]);

        before_method_call:

        $result = call_user_func_array(array($object, $name), $params);

        if ($this->_cacheNext)
        {
            $cache->save($result, $key, array($this->_moduleName)); // Ключ - имя модуля
        }

        before_return:
        $this->_cacheNext     = false;
        $this->_cacheAtomNext = false;

        return $result;
    }

    public function init()
    {

    }

    /**
     * Выборка объекта-обертки для кещирования.
     *
     * @param $methodName имя метода, который будет закеширован
     * @param null $cacheAdapter адаптер для кеширования todo внедрить это
     * @return \Alib\CacheObject
     */
    public function getCacheObject($methodName, $cacheAdapter = null)
    {
        $cacheObject = new \Alib\CacheObject($this, $methodName);
        return $cacheObject;
    }

    /**
     * Возврат объекта - записи.
     *
     * На существование класса не проверяется!
     *
     * @param $name имя таблицы в группе
     * @param null $group имя группы
     * @return \Alib\Record\AbstractClass\Record
     */
    public function getRecordObject($name, $group = null, $module = null)
    {
        if (!$module)
            $module = $this->_moduleName;
        $this->_prepareObjects();
        $group = $this->getGroupObject($group);
        /* @var $group \Alib\Db\Group */
        return $group->getTable($name)->getRecordObject($module);
    }

    public function setRealizations($data)
    {
        $this->_groupsRealizations = array();
        $this->_groupsObjects = array();
        if (is_string($data))
        {
            if (count($this->_groups) != 1)
            {
                throw new Exception('При передаче параметра должена быть одна и только одна группа', 1);
                $this->_groupsRealizations[current($this->_groups)] = $data;
            }
        }
        else if (is_array($data))
        {
            if (count($data) != count($this->_groups))
                throw new Exception('Необходимо указать реализацию для всех групп в модели.', 1);
            foreach($data as $key => $value)
            {
                if (!in_array($key, $this->_groups))
                    throw new Exception('Передана реализация для группы, которая не используется в модели.', 1);
                $this->_groupsRealizations[$key] = $value;
            }
        }
        else
        {
            throw new Exception('Недопустимый формат ', 1);
        }
        return $this;
    }

    protected function _getSubObject($name)
    {
        $nameTable = $name;
        $parts = explode('-', $name);
        if (count($parts) > 1)
        {
            $name = '';
            foreach($parts as $value)
            {
                $name .= ucfirst($value);
            }
        }
        else
            $name = ucfirst($name);
        if (array_key_exists($name, $this->_subObjects))
            goto before_return;
        $className = get_class($this) . '\\' . $name;
        $this->_subObjects[$name] = new $className($this);
        //$this->_subObjects[$name]->setTable(strtolower($nameTable));

        before_return:
        return $this->_subObjects[$name];
    }

    /**
     * Возврат массива объектов групп, необходимых для работы модули.
     *
     * @return array
     */
    public function getGroupsObjects()
    {
        return $this->_groupsObjects;
    }

    /**
     * Выбрать объект - связанную группу таблиц в базе данных.
     *
     * @param string $name имя группы как diary-public или null, тогда первая встречная группа
     * @return \Alib\Db\Group
     */
    public function getGroupObject($name = null)
    {
        $this->_prepareObjects();
        if (!$name)
            return current($this->_groupsObjects);
        if (!isset($this->_groupsObjects[$name]))
            throw new Exception('Запрашивается несуществующая в модели группа.', 1);
        return $this->_groupsObjects[$name];
    }

    /**
     *
     *
     * @param $nameTable
     * @param null $nameGroup
     * @return \Alib\Db\Table
     */
    public function getTableObject($nameTable, $nameGroup = null)
    {
        $group = $this->getGroupObject($nameGroup);
        return $group->getTable($nameTable);
    }


    public function getGroupAdapter($name = null)
    {
        $this->_prepareObjects();
        $group = current($this->_groupsObjects);
        return $group->getAdapter();
    }


    protected function _prepareObjects()
    {
        if (count($this->_groupsObjects))
            return $this;
        foreach($this->_groupsRealizations as $key => $value)
        {
            $this->_groupsObjects[$key] = \Alib\Db\Factory::group($key, $value);
        }
        return $this;
    }

}
