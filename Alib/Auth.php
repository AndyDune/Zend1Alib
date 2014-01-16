<?php
/**
 * 
 */
namespace Alib;
class Auth extends \Zend_Auth
{
    use \Alib\System\Traits\BuildClassName;
    /**
     * @var User
     */
    protected $_user = null;
    protected $_userChecked = false;


    protected $_userRecords = [];

    /**
     * Установленный системны пользователь.
     * Необходим для выполнения задач системы от своего имени.
     * Крон и так далее.
     *
     * @var null|array
     */
    protected $_systemUserRecord = null;

    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    protected function __construct()
    {
       $this->setStorage(new \Zend_Auth_Storage_Session('rznw_auth_store'));
    }

    /**
     * Singleton instance
     *
     * @var Zend_Auth
     */
    protected static $_instance = null;
    
    
    protected static $_instanceUserAutAddData = null;


    /**
     * Returns an instance of Zend_Auth
     *
     * Singleton pattern implementation
     *
     * @return Auth Provides a fluent interface
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public static function getSessionAddData()
    {
        if (null === self::$_instanceUserAutAddData) {
            self::$_instanceUserAutAddData = new \Zend_Session_Namespace('auth_user_add_data');
        }
        return self::$_instanceUserAutAddData;
        
    }

    public function getSystemUser()
    {
        return $this->_systemUserRecord;
    }

    public function setSystemUser($data)
    {
        $this->_systemUserRecord = $data;
        return $this;
    }

    public function hasSystemUser()
    {
        if ($this->_systemUserRecord == null)
            return false;
        else
            return true;
    }


    /**
     * Выборка записей текущего пользователя.
     * Запись создается только одни раз.
     */
    public function getRecord($name = 'data')
    {
        if (isset($this->_userRecords[$name]))
            goto end;
        $group = 'users';
        $realization = 'base';
        $module = 'Passport';
        $this->_userRecords[$name] = $this->_getRecordObject($name, $group, $realization, $module);
        end:
        $user = $this->getIdentity();
        if (!$this->_userRecords[$name]->getId and $user)
        {
            if ($name == 'data')
                $this->_userRecords[$name]->retrieve($user['id']);
            else
            {
                $this->_userRecords[$name]->setUserDataRecord($this->getRecord('data'));
            }
        }
        return $this->_userRecords[$name];
    }


    /**
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_userChecked)
            goto before_return;
        if ($identity = $this->getIdentity())
            $this->_user = new User($this->getIdentity());
        $this->_userChecked = true;
        before_return:
        return $this->_user;
    }


}