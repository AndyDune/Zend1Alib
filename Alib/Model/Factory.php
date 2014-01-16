<?php
/**
 * Фабрика моделей.
 *
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.04.12
 * Time: 14:51
 */
namespace Alib\Model;
class Factory
{
    /**
     * Массив уже загруженных моделей.
     * Ключ собирается из названия модели и модуля.
     *
     * @var array
     */
    protected $_models = array();

    static protected $_instance = null;

    protected $_modelClassPrefix = 'Application\\';

    protected function __construct()
    {

    }

    /**
     *
     * Singleton pattern implementation
     *
     * @return Factory
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Возврат модели модуля.
     *
     *
     * @static
     * @param $name имя модели
     * @param string $module в нижнем регистре
     * @return mixed
     */
    public static function getModel($name, $module = 'www', $type = 'Data', $realization = 'base')
    {
        $object = Factory::getInstance();

        /** @var $model \Alib\Model\AbstractClass\Base */
        $model = $object->get($name, $module, $type, $realization);
        $model->setEventManager(\Alib\EventManager::getInstance());

        return $model;
    }

    public function get($name, $module = 'Www', $type = 'Data', $realization = 'base')
    {
        if (!$type)
            $type = 'Data';
        $module = ucfirst($module);
        $type = ucfirst($type);
        $key = $name . '+' . $module . '+' . $type;
        if (array_key_exists($key, $this->_models))
            return $this->_models[$key];
        $class_name = $this->_modelClassPrefix . $module . '\\Model\\' . $type . '\\' . $this->_formatName($name);
        $this->_models[$key] = new $class_name($realization);
        return $this->_models[$key];
    }



    protected function _formatName($part)
    {
        $parts = explode('-', $part);
        $result = '';
        foreach($parts as $value)
        {
            $result .= ucfirst($value);
        }
        return $result;
    }

}