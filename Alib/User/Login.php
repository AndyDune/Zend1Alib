<?php
/*
 * Вход пользователя.
 * Используются адаптеры для входом разных типов.
 * 
 * 
 * История
 * 
 *   2011-06-20 Добавлено обновление пирога при входе
 *   2011-06-17 Создан
 *
 */
namespace Alib\User;
//use rzn\lib\www as lib;
class Login implements \Zend_Auth_Adapter_Interface
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
     * @var User_Adapter
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
     * @return User_Adapter 
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
    
    /**
     * Performs an authentication attempt
     *
     * @throws Zend_Auth_Adapter_Exception If authentication cannot be performed
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        $data = $this->_adapter->getData();
        $messages_auth = array();
        if ($data)
        {
            if ($data instanceof \Zend_Db_Table_Row_Abstract)
                $data = $data->toArray();
            $code_auth = 1;
        }
        else
            $code_auth = 0;
        
        $result = new \Zend_Auth_Result($code_auth, $data, $messages_auth);
        return $result;
    }    

    
    /**
     *
     * @return array|null 
     */
    public function login()
    {
//        print_r($this->_adapter);
//        print_r(func_get_args());
//        die();
        return call_user_func_array(array($this->_adapter, 'login'), func_get_args());
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
