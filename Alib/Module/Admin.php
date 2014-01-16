<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.10.12
 * Time: 12:05
 *
 *
 */
namespace Alib\Module;
class Admin
{
    use \Alib\System\Traits\BuildClassName;

    protected $_moduleName = null;

    protected $_moduleRoot = null;
    protected $_rootModules = null;

    /**
     * @var \Application\Admin\Library\Controller\Action
     */
    protected $_adminController = null;


    protected $_controllerName = '';
    protected $_actionName     = '';


    public function __construct($module = null)
    {
        $reg = \Alib\Registry::getInstance();
        $this->_rootModules = $reg->get('dir') . '/modules/';
        if ($module)
        {
            $this->setModule($module);
        }
    }

    /**
     * @param \Application\Admin\Library\Controller\Action $controller
     * @return Admin
     */
    public function setAdminController(\Application\Admin\Library\Controller\Action $controller)
    {
        $this->_adminController = $controller;
        return $this;
    }

    public function setController($controller)
    {
        $this->_controllerName = $controller;
        return $this;
    }

    public function setAction($action)
    {
        $this->_actionName = $action;
        return $this;
    }

    public function render()
    {
        /**
         *
         */
        $controllerNameCamel = $this->_buildCamelName($this->_controllerName) . 'Controller';
        $actionNameCamel     = $this->_buildCamelName($this->_actionName, 1) . 'Action';

        $path = $this->_buildControllerPath($controllerNameCamel);
        $controllerClassName = $this->_buildControllerName($controllerNameCamel);
        if (!class_exists($controllerClassName))
        {
            include($path);
            if (!class_exists($controllerClassName))
                throw new \Alib\Exception('Не найдено класса ' . $controllerClassName  . '  ');
        }
        /** @var $object \Alib\Module\AdminController */
        $object = new $controllerClassName($this->_adminController);
        $object->init();
        $reflection = new \ReflectionObject($object);
        if (!$reflection->hasMethod($actionNameCamel))
            throw new \Alib\Exception('Нет метода в административном контроллере модуля.', 1);
        $object->{$actionNameCamel}();
        if ($object->isRenderLocalView())
        {
            $script = $this->_moduleRoot . 'views/scripts/' . $this->_controllerName;
            $view = $this->_adminController->initView();
            $path = $view->getScriptPaths();
            $view->setScriptPath($script);

            $viewScript = $object->getViewScriptName();
            if (!$viewScript)
            {
                $viewScript = $this->_actionName;
            }

            return $view->render($viewScript . '.phtml');
            $view->setScriptPath($path);
        }
        return '';
    }


    public function setModule($module)
    {
        $this->_moduleName = ucfirst($module);
        $this->_moduleRoot = $this->_rootModules . $this->_moduleName . '/admin/';
        return $this;
    }

    public function getMenu()
    {
        $menuPath = $this->_moduleRoot . '/menu.php';
        if (is_file($menuPath))
        {
            $array = include($menuPath);
            return $this->_processAdminMenu($array);
        }
        return null;
    }


    protected function _buildControllerPath($controllerNameCamel)
    {
        $script = $this->_moduleRoot . '/controllers/' . $controllerNameCamel . '.php';
        if (!is_file($script))
            throw new \Alib\Exception('Не существует запрешиваемого файла с контроллером: ' . $script, 1);
        return $script;
    }

    protected function _buildControllerName($controllerNameCamel)
    {
        $class = '\\Application\\' . $this->_moduleName  . '\\Admin\\' . $controllerNameCamel;
        return $class;

    }

    protected function _processAdminMenu($array, $module = null, $controller = null)
    {
        foreach($array as $key => $value)
        {
            if (!array_key_exists('controller', $value) and !$controller)
                $value['controller'] = 'module';
            if (array_key_exists('module', $value))
            {
                $parts = explode(',', $value['module']);
                if (count($parts) < 2)
                {
                    $module = $this->_moduleName . ',' .  $value['module'];
                    $value['module'] = $module;
                }
                $module = $value['module'];
            }
            else if($module)
            {
                $value['module'] = $module;
            }
            if (array_key_exists('menu', $value) and is_array($value['menu']) and count($value['menu']))
            {
                $value['menu'] = $this->_processAdminMenu($value['menu'], $module, $value['controller']);
            }
            $array[$key] = $value;
        }
        return $array;
    }


}
