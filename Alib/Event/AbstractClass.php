<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 10.12.12
 * Time: 14:35
 *
 * Абстрактнгый клас для событий модулей.
 *
 */
namespace Alib\Event;
abstract class AbstractClass
{
    /**
     * @var \Alib\Record\AbstractClass\Record
     */
    protected $_target;

    protected $_arguments;

    /**
     * Имя текущего модуля
     *
     * @var string
     */
    protected $_module = '';

    final public function __construct($target, $argv = array())
    {
        $this->_target = $target;
        $this->_arguments = $argv;
        $this->execute();
    }

    public function execute()
    {

    }

    /**
     * @return \Alib\Record\AbstractClass\Record
     */
    final public function getTarget()
    {
        return $this->_target;
    }

    public function isRecord($table, $group, $module = null)
    {
        if (!$module)
            $module = $this->getModuleName();
        else
            $module = ucfirst($module);

        $className = get_class($this->_target);

        if ($className == 'Application\\' . $module  .'\\Record\\' . $group . '\\' . $table )
        {
            return true;
        }
        return false;
    }

    public function getModuleName()
    {
        if ($this->_module)
            goto before_exit;

        $className = get_class($this);
        $parts = explode('\\', $className);
        $this->_module = $parts[1];

        before_exit:
        return $this->_module;
    }


}
