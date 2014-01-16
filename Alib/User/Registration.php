<?php
/*
 * Регистрация пользователя.
 * Используются адаптеры для регистрации разных типов.
 * 
 * 
 * История
 *   2011-07-17 Создан
 *
 */
namespace Alib\User;
//use rzn\lib\www as lib;
class Registration
{
    
    protected $_tableUserName = 'data';
    protected $_tableUserGroup = 'users';
    protected $_tableUserRealization = 'base';
    
    /**
     * Данные пользователя
     *
     * @var array
     */
    protected $_data = null;
    
    /**
     * Адаптер регистрации и авторизации пользователя.
     * 
     * @var Adapter
     */
    protected $_adapter = null;
    
    
    /**
     * Authentication result code
     *
     * @var int
     */
    protected $_codeAuth = 0;


    /**
     * An array of string reasons why the authentication attempt was unsuccessful
     * If authentication was successful, this should be an empty array.
     *
     * @var array
     */
    protected $_messagesAuth = array();    

    public function __construct($type = null, $strictly = false)
    {
        $this->_adapter = new Adapter($type, $strictly);
    }
    
    /**
     * Возврат объекта адаптера для 
     * 
     * 
     * @return Adapter
     */
    public function getAdapter() 
    {
        return $this->_adapter;
    }


    public function setType($type, $strictly = false)
    {
        $this->_adapter->setType($type, $strictly);
        return $this;
    }
    
    
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->_adapter, $name), $arguments);
    }
    
    
    
    /**
     * Возврат данных пользователя при успешнов входе.
     *
     * @return array|null
     */
    public function getData()
    {
        return $this->_adapter->getData();
    }    
    
}

