<?php
/* 
 * Доступ к зоне сессии, хранящей данные для формы
 */

namespace Alib\Session;
class FormData extends AbstractClass
{
    /**
     * Namespace - which namespace this instance of zend-session is saving-to/getting-from
     *
     * @var string
     */
    protected $_namespaceSpecial = "Special_FormData";


    /**
     * Конструктор, настоенный по умолчанию на сспециальные параметры.
     *
     * @param string $namespace желалетьно не менять
     * @param boolean $singleInstance желательно неменять всегда синглетон.
     */
    public function __construct($namespace = 'Special_FormData', $singleInstance = false)
    {
        parent::__construct($namespace, $singleInstance);
    }


    public function setAll(array $array)
    {
        $_SESSION[$this->_namespace] = $array;
        return $this;
    }

}


