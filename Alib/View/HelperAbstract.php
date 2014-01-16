<?php
/**
 * V.03
 * Базовый абстрактный класс помощника вида.
 *
 * История:
 *
 * 2011-09-01 Важное изменение! Хелпер в папке display не работал.
 * 
 * 2011-07-13 Вызов файлов-отображаторов через магический метод.
 * 
 * 2011-06-17 Метод дисплей может сработать как накопитель $this->_html для этого 3-й параметр в true
 * 2011-04-04 Метод _beforeObjectEcho() - запуск до печати объекта - нужен для перегрузки
 *
 * Создан: 2011-03-15
 *
 *
 * @package
 * @category
 * @author      Andrey Ryzhov <webmaster@rzn.info>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 *
 *
 */
namespace Alib\View;
abstract class HelperAbstract extends \Zend_View_Helper_Abstract
{
    protected  $_html = '';
    
    protected  $_addToHtml = false;

    /**
     *
     * @var \Alib\EventManager
     */
    protected $_events = null;


    public function __construct()
    {
        $this->setEventManager(\Alib\EventManager::getInstance());
        $this->init();
    }

    public function init()
    {

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


    public function __call($name, $arguments) 
    {
        if (substr($name, 0, 7) == 'display' and substr($name, 7, 1) != '_')
        {
            $method = '_display' . ucfirst(substr($name, 7));
            ob_start();
            $res = call_user_func_array(array($this, $method), $arguments);
            if ($this->_addToHtml)
                $this->_html .= ob_get_clean();
            else
                $this->_html = ob_get_clean();
            return $this->_html;
        }
        else
        {
            return call_user_func_array(array($this, 'direct'), $arguments);
        }
        throw new \Alib\Exception('Вызван несуществуюший метод');
    
    }


    /**
     * @param $name
     * @param string $module
     * @return \Alib\Model\AbstractClass\Base;
     */
    final public function getModel($name, $module = 'Www', $type = 'Data')
    {
        return \Alib\Model\Factory::getModel($name, $module, $type);
    }


    protected function setAddToHtml($value = true)
    {
        $this->_addToHtml = $value;
        return $this;
    }

    /**
      * 
      *  ! Метод устарел - не использовать
     *
     * @param string $name постфикс имени метода для вызова.
     * @param array $params параметры для передачи в метод
     * @param boolean $add флаг накопления
     * @return string
     */
    protected function  _display($name = 'base', $params = array(), $add = false)
    {
        $method_name = '_display' . ucfirst($name);
        ob_start();
        echo $this->$method_name($params);
        if ($add)
            $this->_html .= ob_get_clean();
        else
            $this->_html = ob_get_clean();
        return $this->_html;
    }

    protected function _afterObjectEcho()
    {
    }

    protected function _beforeObjectEcho()
    {
    }

    public function get($full = false)
    {
        if ($full)
            return $this->__toString();
        return $this->_html;
    }
    

    public function  __toString()
    {
        $this->_beforeObjectEcho();
        $html = $this->_html;
        $this->_html = '';
        $this->_afterObjectEcho();
        return $html;

    }

}


