<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 07.06.12
 * Time: 14:22
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Navigation;
class MenuTree
{
    protected $_array              = array();

    protected $_arrayCurrentActive = array();

    protected $_controller = 'index';
    protected $_action = 'index';
    protected $_record = '';

    protected $_module = '';

    protected $_done = false;

    public function __construct($data)
    {
        $this->_array = $data;
    }

    public function setParams($controller = null, $action = null)
    {
        $this->_done = false;
        if (!$controller)
            $controller = 'index';
        if (!$action)
            $action = 'index';

        if (isset($_GET['record']))
        {
            $this->_record = $_GET['record'];
        }

        if (isset($_GET['module']))
        {
            $this->_module = $_GET['module'];
        }

        $this->_controller = $controller;
        $this->_action = $action;
        return $this;
    }

    public function get()
    {
        if ($this->_done)
            return $this->_arrayCurrentActive;
        $result = $this->_extractResults($this->_array);
        return $this->_arrayCurrentActive = $result['array'];
    }


    protected function _extractResults($array, $controller = 'index')
    {
        $have_current = false;
        foreach($array as $key => $value)
        {
            $array[$key]['current'] = false;
            if (!isset($value['controller']) or !$value['controller'])
                $value['controller'] = $array[$key]['controller'] = $controller;
            if (!isset($value['action']) or !$value['action'])
                $array[$key]['action'] = $value['action'] = 'index';

            if (!isset($value['record']))
                $array[$key]['record'] = $value['record'] = '';

            if (!isset($value['module']))
                $array[$key]['module'] = $value['module'] = '';


            if (isset($value['menu']) and is_array($value['menu']) and count($value['menu']))
            {
                $res = $this->_extractResults($value['menu'], $value['controller']);
                $array[$key]['menu'] = $res['array'];
                $array[$key]['current'] = $res['current'];
                if ($res['current'])
                    $have_current = true;
            }
            if (!$array[$key]['current'])
            {
                if ($value['controller'] == $this->_controller
                    and $value['action'] == $this->_action
                    and $value['record'] == $this->_record
                    and $value['module'] == $this->_module
                   )
                    $have_current = $array[$key]['current'] = true;
            }
        }
        $result = ['current' => $have_current, 'array' => $array];
        return $result;
    }


}
