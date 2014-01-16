<?php

namespace Alib\Auth\Oauth;
abstract class AbstractClass
{
    const ERROR_GET_SIDED_DATA    = 'errorAuthGetDataSided';
    const ERROR_AUTH_SIDED  = 'errorAuthSided';
    const GOOD_AUTH_SIDED  = 'goodAuthSided';
    
    protected $_messageTemplates = array
    (
        self::ERROR_GET_SIDED_DATA => "Ошибка передачи данных со стороннего сервиса.",
        self::ERROR_AUTH_SIDED => "Авторизация провалена на стороннем сервисе.",
        self::GOOD_AUTH_SIDED => "Авторизация прошла успешно.",
    );

    protected $_message = '';


    /**
     * Уставновка сообщения об операции.
     * @param type $message
     * @return Auth_Oauth_Abstract 
     */
    protected function _setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }
    
    public function getMessage()
    {
        return $this->_message;
    }
    
}