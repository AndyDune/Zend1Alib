<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 29.11.12
 * Time: 13:10
 *
 * Накопитель.
 *
 */
namespace Alib\View\Helper;
use Alib;
class Accumulator extends \Alib\View\HelperAbstract
{
    protected $_data = [];

    protected $_currentNamespace = '';

    public function direct($namespace = null)
    {
        $this->_currentNamespace = $namespace;
        return $this;
    }

    public function add($value, $namespace = null)
    {
        if (!$namespace)
            $namespace = $this->_currentNamespace;
        if (!$namespace)
            throw new \Alib\Exception('Не указано пространство для накопителя.');
        if (!array_key_exists($namespace, $this->_data))
        {
            $this->_data[$namespace] = [];
        }
        $this->_data[$namespace][] = $value;
        return $this;
    }

    public function set($value, $namespace = null)
    {
        if (!$namespace)
            $namespace = $this->_currentNamespace;
        if (!$namespace)
            throw new \Alib\Exception('Не указано пространство для накопителя.');
        $this->_data[$namespace] = $value;
        return $this;
    }

    public function get($namespace, $default = null)
    {
        if (array_key_exists($namespace, $this->_data))
        {
            return $this->_data[$namespace];
        }
        return $default;
    }
}