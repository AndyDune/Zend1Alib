<?php
/* 
 * Для работы с сессиями. Расширяет Zend
 * Нужен для хорошего использования версий.
 */

namespace Alib;
abstract class Session extends \Zend_Session
{
    /**
     *
     * @param string $namespace
     * @return \Zend_Session_Namespace
     */
    public static function getNamespace($namespace = 'Default')
    {
        return new \Zend_Session_Namespace($namespace);
    }
}