<?php
/**
 * Накопитель.
 * Используется для накопления: хлебных крошек, заголовка страницы (если не из структуры) и т.д
 *
 * Реальные методы реализованы только для title.
 * Сходный функционал для  других типов значений реализоан через __call
 *
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 12.03.12
 * Time: 23:39
 */
namespace Alib;
class Accumulator
{

    protected $_data = array();

    /**
     * @var Accumulator
     */
    static protected $instance = null;

    /**
     *
     *
     * @return Accumulator
     */
    static function getInstance()
    {
        if (static::$instance == null)
        {
            static::$instance = new static();

        }
        return static::$instance;
    }

    protected function __construct()
    {

    }

    public function addTitle($value)
    {
        $this->_checkKey('title');
        $this->_data['title'][] = $value;
        return $this;
    }

    public function setTitle($value)
    {
        $this->_checkKey('title');
        $this->_data['title'] = array($value);
        return $this;
    }

    public function getTitle()
    {
        $this->_checkKey('title');
        return $this->_data['title'];
    }

    public function addBreabcrumb($title, $url, $mode = 0)
    {
        $key = 'breabcrumb';
        $this->_checkKey($key);
        $this->_data[$key][] = array('title' => $title, 'url' => $url, 'mode' => $mode);
        return $this;
    }

    public function setBreabcrumb($title, $url, $mode = 0)
    {
        $key = 'breabcrumb';
        $this->_checkKey($key);
        $this->_data[$key][] = array('title' => $title, 'url' => $url, 'mode' => $mode);
        return $this;
    }

    public function getBreabcrumb()
    {
        $key = 'breabcrumb';
        $this->_checkKey($key);
        return $this->_data[$key];
    }



    public function setH1($title)
    {
        $key = 'h1';
        $this->_checkKey($key);
        $this->_data[$key] = array($title);
        return $this;
    }

    public function getH1()
    {
        $key = 'h1';
        $this->_checkKey($key);
        $acc = '';
        foreach($this->_data[$key] as $value)
        {
            $acc .= $value;
        }
        return $acc;
    }





    public function __call($name, $arguments)
    {
        $choice = substr($name, 0, 3);
        $name = substr($name, 3);
        if ($choice == 'set' or $choice == 'add')
        {
            $choice = '_' . $choice;
            return $this->{$choice}(strtolower($name), $arguments[0]);
        }
        else if ($choice == 'get')
        {
            return $this->_get(strtolower($name));
        }
        throw new Exception('Вызван несуществуюший метод');
    }


    protected function _get($name)
    {
        $this->_checkKey($name);
        return $this->_data[$name];
    }

    protected function _add($name, $value)
    {
        $this->_checkKey($name);
        $this->_data[$name][] = $value;
        return $this;
    }

    protected function _set($name, $value)
    {
        $this->_checkKey($name);
        $this->_data[$name] = array($value);
        return $this;
    }



    protected function _checkKey($name)
    {
        if (!isset($this->_data[$name]))
            $this->_data[$name] = array();
    }

}
