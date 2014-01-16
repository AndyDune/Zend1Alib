<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.05.12
 * Time: 13:31
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\System\Traits;
use Alib\String;
trait BuildClassName
{
    protected function _buildNameForRecordClass($table, $group, $module = 'Www')
    {

        $prefix = '\\Application\\' . ucfirst($module) . '\\Record\\'
                . $this->_buildCamelName($group)
                . '\\'
                . $this->_buildCamelName($table)
                ;
        return $prefix;
    }

    /**
     * @param $table
     * @param null $group
     * @param string $realization
     * @param string $module
     * @return \Alib\Record\AbstractClass\Record
     * @throws \Alib\Exception
     */
    protected function _getRecordObject($table, $group = null, $realization = 'base', $module = 'Www')
    {
        if ($group !== null)
            goto go_next;

        $explode = new String\Explode($table, ',');
        if ($explode->make() != 4)
        {
            throw new \Alib\Exception('Число параметров для создание объекта записи должно быть 4', 1);
        }
        $table       = $explode[0];
        $group       = $explode[1];
        $realization = $explode[2];
        $module      = $explode[3];

        go_next:

        $name = $this->_buildNameForRecordClass($table, $group, $module);
        $record = new $name($realization);
        $record->setEventManager(\Alib\EventManager::getInstance());
        $record->initTable($realization);
        return $record;
    }


    protected function _buildCamelName($name, $firstNoUp = false)
    {
        $result = '';
        $parts = explode('-', $name);
        $first = true;
        foreach($parts as $value)
        {
            if ($firstNoUp and $first)
                $result .= $value;
            else
                $result .= ucfirst($value);
            $first = false;
        }
        return $result;
    }

    protected function _destroyCamelName($name)
    {
        $result = '';
        $len = strlen($name);
        for($pointer = 0; $pointer < $len; $pointer++)
        {
            $char = substr($name, $pointer, 1);
            if ($char === strtoupper($char))
            {
                if ($result)
                    $result .= '-';
                $char = strtolower($char);
            }
            $result .= $char;
        }
        return $result;
    }

}