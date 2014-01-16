<?php
/*
 *
 * История
 *   2011-06-17 Создан
 *   2011-06-20 Добавлено обновление пирога при входе
 *
 */
namespace Alib\User;
use Alib;
use Alib\Cookie\ArraySingleton;

class AutoEnter
{
    protected $_key      = '';
    protected $_password = '';
    protected $_user     = null;
    
    
    protected $_cookieName  = 'aerznw';


    public function __construct($user = null)
    {
        //echo 'Заменить на рекорд'; die();
        $this->_user    = $user;
    }
    
    public function store($user = null)
    {
        $this->_key     = md5(uniqid());
        $this->_password = md5(uniqid());
        
        if ($user)
            $this->_user = $user;
        if (!$this->_user)
            return false;
        
        if ($this->_toCookie())
        {
            $this->_toDb();
            return true;
        }
        return false;
    }

    
    public function login()
    {
        $cooc = ArraySingleton::getInstance($this->_cookieName);
        if (!$cooc['key'] or !$cooc['password'])
            return null;

        $select = Alib\Db\Factory::select('auto-enter', 'users');
        $select->addFilterAnd('key', $cooc['key']);
        $data = $select->get(1);
        if (!$data or !count($data))
        {
            $cooc->clear();
            return false;
        }
        $data = $data->current();
        if ($data['password'] != $cooc['password'])
        {
            $cooc->clear();
            return false;
        }
        // Обновление момента автовхода
        $table = Alib\Db\Factory::table('auto-enter', 'users');
        $table->updateWithId(array('date' => date('Y-m-d')), $cooc['key']);
        
        $auth = Alib\Auth::getInstance();
        $user = new Login('directly');
        $user->login($data['user_id']);
        $auth->authenticate($user);
        
        
        $cooc['time'] = time();
        $cooc->set();
        return true;
    }
    

    public function logOut()
    {
        $cooc = ArraySingleton::getInstance($this->_cookieName);
        if ($cooc['key'])
        {        
            $table = Alib\Db\Factory::table('auto-enter', 'users');
            $table->deleteWithId($cooc['key']);
        }
        $cooc->clear();
    }    
    
    public function _toCookie()
    {
        $cooc = ArraySingleton::getInstance($this->_cookieName);
        $cooc['key'] = $this->_key;
        $cooc['password'] = $this->_password;
        $cooc->set();
        return true;
    }
    
    public function _toDb()
    {
        $table = Alib\Db\Factory::table('auto-enter', 'users');
        $data = array(
            'user_id'  => $this->_user['id'],
            'key'      => $this->_key,
            'password' => $this->_password,
        );
        $table->insert($data);
        return true;
    }
    
    
}