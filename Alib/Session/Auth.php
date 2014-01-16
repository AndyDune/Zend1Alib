<?php
/*
 * Доступ к зоне сессии, хранящей сообщения
 */
namespace Alib\Session;
class Auth extends AbstractClass
{
    /**
     * Namespace - which namespace this instance of zend-session is saving-to/getting-from
     *
     * @var string
     */
    protected $_namespaceSpecial = "Special_Session_Auth";


    /**
     * Конструктор, настоенный по умолчанию на сспециальные параметры.
     *
     * @param string $namespace 
     * @param boolean $singleInstance желательно неменять всегда синглетон.
     */
    public function __construct($namespace = 'Common')
    {
        $this->_namespaceSpecial .= '_' . $namespace;
        parent::__construct($namespace);
    }
    
}
