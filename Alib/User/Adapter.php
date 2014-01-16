<?php
/**
 * V.02
 * Формирование имени адаптера для 
 * 
 * Версии:
 * 2011-09-12 Адаптер direct. Используется для перезахода.
 * 2011-08-04 Изменение механизма пердачи параметров в адаптеры.
 * 
 * 
 */
namespace Alib\User;
use Alib\Db;
class Adapter
{
    /**
     * Error constants
     */
    const ERROR_RICH_TRY_LIMIT = 'richTryLimit';
    const ERROR_NO_DATA        = 'noData';
    const ERROR_WRONG_PASSWORD = 'wrongPassword';
    const ERROR_WRONG_MODE     = 'wrongMode';
    
    /**
     * Пользователь заблокирован. Вход невозможен.
     */
    const ERROR_USER_BLOCK = 'errorUserBlock';
    const GOOD_ENTER       = 'userGoodEnter';
    const GOOD_EXIT        = 'userGoodExit';
    
    const ERROR_NO_NEED_VERIFICATION =  'verificationNoNeed';
    const GOOD_VERIFICATION =  'userGoodVerification';
    const ERROR_BAD_VERIFICATION =  'userBadVerification';
    const GOOD_PASSWORD_CHANGE =  'userGoodPasswordChange';
    
    
    const ERROR_NO_PASSWORD_RECALL = 'userNoPasswordRecall';
    const ERROR_BAD_PASSWORD_RECALL = 'userBadPasswordRecall';
    const GOOD_PASSWORD_RECALL_ORDER = 'userGoodPasswordRecallOrder';
    const GOOD_PASSWORD_RECALL_MAKE  = 'userGoodPasswordRecallMake';
    
    const ERROR_NO_TIME_REST = 'errorNoTimeRest';
    
    
    
    /**
     * Ошибка получения данных со стороннего сервера.
     */
    const ERROR_NO_DATA_FROM_SIDED =  'errorNoDataFromSided';
    
    /**
     * Ошибка авторизации пользователя через стронний сервис.
     */
    const ERROR_SIDED_AUTH = 'errorSidedAuth';

    /**
     * @var array Message templates
     */
    protected $_messageTemplates = array(
        self::ERROR_RICH_TRY_LIMIT => "Достигнуто максивальное коллическо попыток за интервал времени.",
        self::ERROR_NO_DATA => "Нет данных по запрошенному ключу.",
        self::ERROR_WRONG_PASSWORD => "Неверный пароль.",
        self::ERROR_WRONG_MODE => "Неверный метод входа.",
        self::GOOD_ENTER => "Пользователь успешно вошел.",
        self::GOOD_EXIT => "Пользователь успешно вышел.",
        self::ERROR_USER_BLOCK => "Пользователь заблокирован. Вход невозможен.",
            
        self::ERROR_NO_NEED_VERIFICATION => "В верификации нет необходимости.",
        self::GOOD_VERIFICATION      => "Верификация пройдена.",
        self::ERROR_BAD_VERIFICATION => "Верификация провалена.",
        self::GOOD_PASSWORD_CHANGE   => "Пароль удачно сменен.",
        
        self::ERROR_NO_PASSWORD_RECALL  => "Напоминание пароля сначала нужно заказать.",
        self::ERROR_BAD_PASSWORD_RECALL => "Напоминание пароля провалено.",
        
        self::GOOD_PASSWORD_RECALL_ORDER     => "Ссылка с секретным кодом на сброс пароля отправлена по почте.",
        self::GOOD_PASSWORD_RECALL_MAKE      => "Пароль успешно сброшен. Новый отослан по email.",
        
        self::ERROR_NO_TIME_REST       => "Операцию нельзя слишком часто повторять.",
        
        self::ERROR_NO_DATA_FROM_SIDED => "Ошибка получения данных со стороннего сервера.",
        self::ERROR_SIDED_AUTH         => "Ошибка авторизации пользователя через стронний сервис.",
        
            
    );
    
    
    protected $_tableUserName = 'data';
    protected $_tableUserGroup = 'users';
    protected $_tableUserRealization = 'base';
    
    protected $_adapterPrefix = 'Alib\\User\\Adapter\\';
    
    protected $_workObject = null;
    
    protected $_error = null;
    
    
    protected $_adapterTypes  = array(
                                      'self'          => 'SelfClass',
                                      'livejournal'   => 'LiveJournal',
                                      'vk'            => 'Vk',
                                      'fasebook'      => 'FaseBook',
                                      'odnoklassniki' => 'Odnoklassniki',
                                      'direct'        => 'Directly',
                                      'directly'      => 'Directly',
                                      'loginza'       => 'Loginza'
                                      );
    
    
    /**
     * Тип входа.
     * По умолчанию с использованием логина и пароля с сайта.
     * 
     * @var string
     */
    protected $_type = 'self';
    
    
    protected $_typeClassNamePart = 'SelfClass';
    
    
    public function __construct($type = null, $strictly = false)
    {
        $this->_userDataTableObject = Db\Factory::table($this->_tableUserName, $this->_tableUserGroup, $this->_tableUserRealization);
        if ($type)
            $this->setType($type, $strictly);
    }
    
    public function setType($type, $strictly = false)
    {
        $this->_workObject = null;
        if (isset($this->_adapterTypes[$type]))
        {
            $this->_typeClassNamePart = $this->_adapterTypes[$type];
        }
        else
        {
            if ($strictly)
                throw new Exception ('Нет такого адаптера: ' . $type, 1);
            $this->_typeClassNamePart = ucfirst($type);
        }
        $this->_type = $type;
        return $this;
    }

    
    public function __call($name, $arguments)
    {
        $object = $this->getWorkObject();
        $object->setMethodArguments($arguments);
        return call_user_func_array(array($object, $name), $arguments);
        return $object->$name();
    }

    
    public function message($code)
    {
        $this->_error = $code;
        return $this;
    }
    
    /**
     * Возврат ошибки
     * 
     * 
     * @return string 
     */
    public function getMessage()
    {
        return $this->_error;
    }
    
    
    public function register()
    {
        $args = func_get_args();
        $object = $this->getWorkObject();
        $object->setMethodArguments($args);
        return call_user_func_array(array($object, 'register'), $args);
        return $object->register();
    }
    
    public function login()
    {
        $args = func_get_args();
        $object = $this->getWorkObject();
        $object->setMethodArguments($args);
        return call_user_func_array(array($object, 'login'), $args);
        return $object->login();
        
    }
    
    /**
     * ВОзврат данных пользователя, если они есть.
     * 
     * @return array|null 
     */
    public function getData()
    {
        $object = $this->getWorkObject();
        $object->setMethodArguments(func_get_args());
        return $object->getData();
        
    }
    
    
    /**
     * Возврат данных о текущем пользователе
     * 
     * @return array 
     */
    public function getCurrentUserData()
    {
        $auth = Auth::getInstance();
        return $auth->getIdentity();
    }
    
    public function getWorkObject()
    {
        if ($this->_workObject === null)
        {
            $name = $this->_adapterPrefix . $this->_typeClassNamePart;
            $this->_workObject = new $name($this);
        }
        return $this->_workObject;
    }    
    
    /**
     * Возврат объекта доступа к таблице пользователя.
     *
     * @return rzn\model\db\abs\www\Data
     */
    public function getUserDataTableObject()
    {
        return $this->_userDataTableObject;
    }    
    
    
}