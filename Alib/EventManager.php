<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 30.09.12
 * Time: 11:46
 *
 * Менеджер обработчиков событий.
 *
 */
namespace Alib;
class EventManager
{

    public $_events = [];

    /**
     * @var Zend_EventManager_StaticEventManager
     */
    protected static $instance;

    /**
     * Singleton
     *
     * @return void
     */
    protected function __construct()
    {
    }

    /**
     * Retrieve instance
     *
     * @return EventManager
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Reset the singleton instance
     *
     * @return void
     */
    public static function resetInstance()
    {
        self::$instance = null;
    }

    public function setEventsArray($array)
    {
        $this->_events = $array;
        return $this;
    }

    public function trigger($event, $target = null, $argv = array(), $callback = null)
    {
        if (!isset($this->_events[$event]) or !count($this->_events[$event]))
            return false;
        $count = 0;
        foreach($this->_events[$event] as $value)
        {
            $eventClassName = $this->_getEventClassName($value);
            $object = new $eventClassName($target, $argv);
            $count++;
        }
        return $count;
    }

    protected function _getEventClassName($params)
    {
        if (isset($params['class']))
            return $params['class'];
        if (isset($params['class_in_module']) and isset($params['module']))
        {
            return '\\Application\\' . ucfirst($params['module']) . '\\Library\\Events\\' . $params['class_in_module'];
        }
        throw new \Alib\Exception('Не указаны необходимые параметры для выбора имени класса для события.', 1);
    }

}
