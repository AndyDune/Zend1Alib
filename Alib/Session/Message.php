<?php
/*
 * Доступ к зоне сессии, хранящей сообщения
 */
namespace Alib\Session;
class Message extends AbstractClass
{
    /**
     * Namespace - which namespace this instance of zend-session is saving-to/getting-from
     *
     * @var string
     */
    protected $_namespaceSpecial = "Special_Message";


    /**
     * Конструктор, настоенный по умолчанию на сспециальные параметры.
     *
     * @param string $namespace желалетьно не менять
     * @param boolean $singleInstance желательно неменять всегда синглетон.
     */
    public function __construct($namespace = 'Special_Message', $singleInstance = false)
    {
        parent::__construct($namespace, $singleInstance);
    }
    
    public function set($text, $code = 0)
    {
        $this->code = $code;
        $this->text = $text;
    }
    
    public function getText()
    {
        return $this->text;
    }

    public function getCode()
    {
        return $this->code;
    }
    
    
}
