<?php
/**
 * Набор методов для пользователя.
 *
 *
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.04.12
 * Time: 10:52
 */
namespace Alib;
class User
{
    protected $_data = null;
    public function __construct($data)
    {
        $this->_data = $data;
    }

    /**
     *
     * @return \Zend_Cache
     */
    public function getCache()
    {
        return Cache::factory('User');
    }
}
