<?php

/**
 * Общий абстрактный класс для ряда классов авторизации и регистрации пользователей.
 * 
 */
namespace Alib\User\Adapter;
use Alib;
abstract class AbstractClass
{
    /**
     * Ссылка на корневой объект объект.
     * 
     * @var Alib\User\Adapter
     */
    protected $_base = null;
    
    protected $_data = null;
    
    
    protected $_arguments = null;


    public function __construct(Alib\User\Adapter $parent)
    {
        $this->_base = $parent;
    }
    
    
    public function register()
    {
        
    }
    
    public function login()
    {
        
    }

    public function logout()
    {
        
    }
    
    public function getUserID()
    {
        if (isset($this->_data['id']))
            return $this->_data['id'];
        return null;
    }    

    
    final public function setMethodArguments($array)
    {
        $this->_arguments = $array;
        return $this;
    }
    
    
    public function getData()
    {
        return $this->_data;
    }

    
    protected function _getUserData($value, $field, $select)
    {
        $select->addFilterAnd($field, $value);
        $data = $select->get(1);
        if (count($data))
        {
            $data = $data->current()->toArray();
        }
        else
            $data = null;
        return $data;
    }    
    
    final protected function _getArgument($number, $required = false)
    {
        // Аргументы в массиве нумеруются с 0, для удобства доступа изменяем это
        // Нумерация с единицы
        if ($number > 0)
            $number--;
        if (isset($this->_arguments[$number]))
            return $this->_arguments[$number];
        if ($required)
            throw new User_Exception('Нет обязательного аргумента под номером: ' . $number . '.', 500);
        return null;
    }
    
    
    
}
