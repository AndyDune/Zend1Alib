<?php

/**
 * V.1.01 (2012-09-27) Добавлено подключение стилей и скриптов.
 * 
 */
namespace Alib\View;
use Alib\Registry;
use Alib\Result;
abstract class ComponentAbstract
{
    use \Alib\System\Traits\BuildClassName;

    /**
     * 
     * @var \Zend_View
     */
    protected  $_view;
    
    protected $_folderInModule = '/views/components/';
    protected $_folderInModuleDefault;
    protected $_modulePath = null;

    /**
     * Модуль в котором располагается контроллер компонента.
     *
     * @var null
     */
    protected $_controllerModule = null;

    /**
     * Модуль контролера - родителя.
     * Нужно заполнить при создании дочернего контроллера компонента из существующего.
     *
     * @var null
     */
    protected $_parentControllerModule = null;


    protected $_sameModuleViewAndController = false;

    /**
     * Путь к набору модулей.
     *
     * @var null
     */
    protected $_modulesPath = null;

    protected $_results = array();

    /**
     * Флаг остановки рендеринга компонента с отменой кеширования.
     *
     * @var bool
     */
    protected $_stop = false;


    /**
     * Промежуточные параметры, которые методы компонента передают шаблонам.
     * @var array
     */
    protected  $_viewParameters = array();

    /**
     * Параметры для подшаблона в пределах компонента.
     *
     * @var array
     */
    protected  $_viewParametersLocal = array();


    /**
     * Входные параметры, которые скармливает компоненту выд при вызове.
     * @var array
     */
    protected  $_inputParameters = array();


    protected $_registry = null;

    /**
     * @var \Alib\Result
     */
    protected $_result = null;


    protected $_useLocalParams = false;

    public function __construct()
    {
        $this->_registry = Registry::getInstance();
        $this->_result = new Result();
    }

    /**
     * Запускается прямо перед вызовом основного process
     * В отличие от конструктора запускается после передачи всех параметров.
     */
    public function init() {}


    public function stop()
    {
        $this->_stop = true;
    }

    public function isStop()
    {
        return $this->_stop;
    }


    /**
     * Добавление результата в набор.
     *
     * @param $name
     * @param $value
     * @return ComponentAbstract
     */
    protected function addResult($name, $value)
    {
        $this->_results[$name] = $value;
        return $this;
    }


    final public function setView($view)
    {
        $this->_view = $view;
        $this->_initAfterSetView();
        return $this;
    }

    protected function _initAfterSetView()
    {
    }


    /**
     *
     * @param string $section Секция компонента. Определяет группу сязных.
     * @param string $name Имя компонента в группе
     * @param string $view Уровень для переключения видов.
     * @return ComponentAbstract 
     */
    final public function setNames($section, $name, $view = 'default')
    {
        $this->_folderInModuleDefault = $this->_folderInModule . $section . '/' . $name . '/default/';
        $this->_folderInModule .= $section . '/' . $name . '/' . $view . '/';
        return $this;
    }

    final public function setParameters($params)
    {
        $this->_inputParameters = $params;
        return $this;
    }

    final public function getParameters()
    {
        return $this->_inputParameters;
    }


    public function getParameter($key, $default = null)
    {
        if (is_array($key))
        {
            foreach($key as $k)
            {
                if (array_key_exists($k, $this->_inputParameters))
                    return $this->_inputParameters[$k];
            }
        }
        else if (array_key_exists($key, $this->_inputParameters))
            return $this->_inputParameters[$key];
        return $default;
    }

    /**
     * Алиас getParameter
     *
     * @param $key
     * @param null $default
     * @return null
     */
    public function getParam($key, $default = null)
    {
        return $this->getParameter($key, $default);
    }


    /**
     * @param $name
     * @param string $module
     * @return \Alib\Model\AbstractClass\Base;
     */
    final public function getModel($name, $module = 'Www', $type = 'Data')
    {
        return \Alib\Model\Factory::getModel($name, $module, $type);
    }

    /**
     * @param $name
     * @param string $module
     * @return \Alib\Model\AbstractClass\Base;
     */
    final public function getRecord($name = 'null', $group = 'null', $realization = 'base', $module = 'Www')
    {
        if (!$realization)
            $realization = 'base';

        return $this->_getRecordObject($name, $group, $realization, $module);
    }


    /**
     * Добавление пути для подключения помощников вида.
     *
     * @param $module
     * @return ComponentAbstract
     */
    public function addHelperPath($module)
    {
        $this->_view->addModuleHelperPath($module);
        return $this;

        // Устаревше
        $name = ucfirst($module);
        $reg = Registry::getInstance();
        $this->_view->addHelperPath($reg->get('dir') . '/modules/' . $name . '/views/helpers', "Application\\" . $name . "\\View\\Helper\\");
        return $this;
    }


    /**
     * Возврат объекта
     * 
     * @return \Zend_view
     */
    final public function view()
    {
        return $this->_view;
    }
    /**
     * Синоним  view()
     *
     * @return \Zend_View
     */
    final public function getView()
    {
        return $this->_view;
    }

    /**
     * Включение файла-шаблона, который расположен в текущей папке.
     * С возможностью выполнения предварительный действий.
     *
     * @param $view имя шаблона и часть имени метода, который при его су3ществовании будет запущен
     * @param null $methodName явный запуск другого метода перед включением шаблона
     * @return string
     */
    final public function render($view, $methodName = null)
    {
        $this->_useLocalParams = true;
        if ($methodName and is_string($methodName))
        {
            $methodNamePart = $methodName;
        }
        else if (isset($methodName['method']))
        {
            $methodNamePart = $methodName['method'];
            unset($methodName['method']);
        }
        else
        {
            $methodNamePart = $this->_buildCamelName($view);
        }
        $method = 'render' . ucfirst($methodNamePart);
        if (is_array($methodName))
            $this->_viewParametersLocal = $methodName;
        if (method_exists($this, $method))
        {
            $this->$method($methodName);
        }
        $html = $this->_processScript($view);
        $this->_useLocalParams = false;
        $this->_viewParametersLocal = array();
        return $html;
    }
    
    
    final public function assign($key, $value)
    {
        $this->_viewParameters[$key] = $value;
        return $this;
    }

    /**
     *
     *
     * @return string
     */
    final public function getParentControllerModule()
    {
        if ($this->_parentControllerModule)
            return $this->_parentControllerModule;
        $reflection = new \ReflectionObject($this);
        $parentClass = $reflection->getParentClass()->getName();
        $parts = explode('\\', $parentClass);
        if ($parts[0] == 'Application')
            $this->_parentControllerModule = $parts[1];
        return $this->_parentControllerModule;
    }


    final public function setControllerModule($module, $viewModule)
    {
        $this->_module = $viewModule;
        $this->_controllerModule = $module;
        if ($this->_module == $this->_controllerModule)
            $this->_sameModuleViewAndController = true;
        return $this;

    }

    final public function setModulesPath($path)
    {
        $this->_modulesPath = $path;
        return $this;
    }

    final public function setModulePath($path)
    {
        $this->_modulePath = $path;
        return $this;
    }
    

    public function __set($key, $value)
    {
        if ($this->_useLocalParams)
            $this->_viewParametersLocal[$key] = $value;
        else
            $this->_viewParameters[$key] = $value;
    }
    

    public function __get($key)
    {
        if ($this->_useLocalParams and isset($this->_viewParametersLocal[$key]))
            return $this->_viewParametersLocal[$key];
        if (isset($this->_viewParameters[$key]))
            return $this->_viewParameters[$key];

        return null;
    }

    public function addCss($file, $module = null, $fromEnd = false)
    {
        $this->_result->addCss($file, $module, $fromEnd);
        return $this;
    }

    public function addJs($file, $module = null)
    {
        $this->_result->addJs($file, $module);
        return $this;
    }


    final protected function _processScript($view = 'index', $exception = true, $postfix = 'phtml')
    {
        ob_start();
        $name = $view . '.' . $postfix;
        $path        = $this->_modulePath . $this->_folderInModule . $name;
        $pathDefault = $this->_modulePath . $this->_folderInModuleDefault . $name;
        if (is_file($path))
        {
            include($path);
            goto end_block;
        }
        if (is_file($pathDefault))
        {
            include($pathDefault);
            goto end_block;
        }
        if (!$this->_sameModuleViewAndController)
        {
//            $path        = $this->_modulesPath . '/' . $this->_controllerModule . $this->_folderInModule . $name;
            $pathDefault = $this->_modulesPath . '/' . $this->_controllerModule . $this->_folderInModuleDefault . $name;
            /*
            if (is_file($path))
            {
                include($path);
                goto end_block;
            }
            */
            if (is_file($pathDefault))
            {
                include($pathDefault);
                goto end_block;
            }
        }
        $parentModule = $this->getParentControllerModule();
        if ($parentModule)
        {
            $pathDefault = $this->_modulesPath . '/' . $parentModule . $this->_folderInModuleDefault . $name;
            if (is_file($pathDefault))
            {
                include($pathDefault);
                goto end_block;
            }
        }
        if ($exception)
            throw new \Alib\Exception('Шаблон компонента не найден: ' . $name, 1);

        end_block:
        return ob_get_clean();
    }


    final public function process($view = 'index')
    {
        $path = $this->_modulePath . $this->_folderInModule;
        ob_start();
        //include($path . $view . '.phtml');

        echo $this->_processScript($view);

        //$add = $path . 'style.css';
        if ($text = $this->_processScript('style', false, 'css'))
        {
            ?><style type="text/css"><?
            echo $text;
            //include($add);
            ?></style><?
        }
        //$add = $path . 'script.js';
        if ($text = $this->_processScript('script', false, 'js'))
        {
            ?><script type="text/javascript"><?
            echo $text;
            //include($add);
            ?></script><?
        }

        $html = ob_get_clean();

        $result = $this->_result;
        $result->setHtml($html);
        $result->setArray($this->_results);

        return $result;
    }
    
}
