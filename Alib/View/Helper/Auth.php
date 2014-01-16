<?php
/**
 * Вывод альтернативы при условии
 *
 * @package
 * @category
 * @author      Andrey Ryzhov <webmaster@rzn.info>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 */
namespace Alib\View\Helper;
use Alib;
class Auth extends Alib\View\HelperAbstract
{

    /**
     * Информация о пользователе, если он авторизован.
     *
     * @var array|null
     */
    protected $_user     = null;

    /**
     * Авторизованный ли пользователь работате с контроллером
     * @var boolean
     */
    protected $_userAuth = false;
    
    /**
     *
     * @var \Zend_Auth
     */
    protected $_auth = false;

    public function __construct()
    {
        $this->_auth = Alib\Auth::getInstance();
        $auth = $this->_auth->getIdentity();
        if ($auth)
        {
            $this->_user           = $auth;
            $this->_userAuth       = true;
        }
        else if (0) // Излищнее
        {
            
            $auto = new Alib\User\AutoEnter();
            $auto->login();
            $auth = Alib\Auth::getInstance()->getIdentity();
            if ($auth)
            {
                $this->_user           = $auth;
                $this->_userAuth       = true;
            }
        }        
    }    
    
    public function direct()
    {
        return $this;
    }

    public function is()
    {
        return $this->_userAuth;
    }
    
    public function getData()
    {
        return $this->_user;
    }

    public function get($key)
    {
        if($this->_user and array_key_exists($key, $this->_user))
            return $this->_user[$key];
        return null;
    }



}