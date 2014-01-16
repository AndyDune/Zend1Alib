<?php

/**
 * Ссылки на файлы видов: Стили, скрипты, картинки
 *
 * Компоненты подключаются только для модуля по умолчаниию.
 * Позже переделать!!
 * 
 * @author dune
 */

namespace Alib\View\Helper;
use Alib\Registry;
use Alib as A;
class IncludeComponent extends \Alib\View\HelperAbstract
{
    protected $_classPrefix1 = '\\Application\\';
    protected $_classPrefix2 = '\\Component\\';
    protected $_classPrefix = 'Www';

    /**
     * Имя модуля в котором лежат виды.
     *
     * @var string
     */
    protected $_module = 'Www';

    /**
     * Имя модуля класса контроллера - не вида.
     * @var string
     */
    protected $_moduleController = 'Www';

    protected $_className = 'Www';

    /**
     *
     * @var \A\View\ComponentAbstract
     */
    protected $_component;
    
    protected $_viewName;
    protected $_parameters;


    protected $_rootModules = '';

    /**
     * @var \Alib\Result
     */
    protected $_resultObject;

    /**
     * Имя текущего модуля
     *
     * @var string
     */
    protected $_currentModule;

    public function  init()
    {
        $reg = A\Registry::getInstance();

        $this->_currentModule = $reg->get('module');

        $this->_rootModules = $reg->get('dir_modules') . '/';
    }

    /**
     *
     * @param string $php имя компонента <раздел>:<имя>[:<модуль>]
     * @param string $view
     * @param array $parameters
     * @param null $control
     * @return IncludeComponent|void
     * @throws \Alib\Exception
     */
    public function direct($php = null, $view = 'default', $parameters = array(), $control = null)
    {
        if (!$view)
            $view = 'default';
        //$this->_module = $this->_classPrefix = 'Www';
        $parts = explode(':', $php);
        if (count($parts) < 2)
            throw new A\Exception('Неверный входной параметр: $php', 10);
        if (isset($parts[2]))
        {
            $this->_module = $this->_classPrefix = ucfirst($parts[2]);
        }
        else
        {
            // Модуль выбирается из текущего
            $this->_module = $this->_classPrefix = $this->_currentModule;
        }
        $this->_moduleController = $this->_module;
        $this->_classPrefix = $this->_classPrefix1 . $this->_classPrefix . $this->_classPrefix2;
        $class = $this->_classPrefix . $this->_formatNamePart($parts[0])
                . '\\' . $this->_formatNamePart($parts[1]);

        $partsView = explode(':', $view);
        $countParts = count($partsView);
        if ($countParts > 2)
            throw new A\Exception('Неверный входной параметр: $view', 10);
        if ($countParts == 2)
        {
            if (!$partsView[0])
                $view = 'default';
            else
                $view = $partsView[0];
            if ($partsView[1])
                $this->_module = ucfirst($partsView[1]);

        }

        $this->_viewName = $view;
        $this->_className = $class;

        $this->_componentNameParts = $parts;


        /* @val \A\View\ComponentAbstract $object
        */
        /*
        $object =  new $class();
        $object->setView($this->view);
        $object->setNames($parts[0], $parts[1], $view);
        $this->_component = $object;
        */

        $this->_parameters = $parameters;

        $this->_processComponent();
        return $this;
    }
    
    protected function _formatNamePart($part)
    {
        $parts = explode('-', $part);
        $result = '';
        foreach($parts as $value)
        {
            $result .= ucfirst($value);
        }
        return $result;
    }


    public function toResult(\Alib\Result $result)
    {
        $result->copyFrom($this->_resultObject);
        return $this;
    }

    protected function _beforeObjectEcho()
    {

    }

    public function setParameter($key, $value = null)
    {
        $this->_parameters[$key] = $value;
        return $this;
    }

    public function getParameter($key)
    {
        if (isset($this->_parameters[$key]))
            return $this->_parameters[$key];
        return null;
    }


    protected function _processComponent()
    {
        $this->events()->trigger('view.component.include',
            $this, ['module' => $this->_module,
                    'class'  => $this->_className,
                    'name_parts' => $this->_componentNameParts,
                    'view'       => $this->_viewName
        ]);
        if (isset($this->_parameters['cache']) and $this->_parameters['cache'])
        {
            //$object =  new \Alib\CacheObject($this->_className, 'process');
            $key = md5($this->_className . '+'
                 . $this->_componentNameParts[0] . '+'
                 . $this->_componentNameParts[1] . '+'
                 . $this->_viewName . '+'
                 . serialize($this->_parameters)
            );
            $cache = \Alib\Cache::factory('component');
            $this->_resultObject = $cache->load($key);
            if ($this->_resultObject)
                goto before_return;

        }
        else
            $key = false;

        $object =  new $this->_className();

        //else
        //    $object =  new $this->_className();

        $object->setView($this->view);
        $object->setNames($this->_componentNameParts[0], $this->_componentNameParts[1], $this->_viewName);
        $this->_component = $object;

        //echo   $path = realpath(__DIR__ . '/../../');
        // Директория, модуля из которого берутся шаблоны
        $path = $this->_rootModules . $this->_module;

        $this->_component->setModulePath($path);

        $this->_component->setModulesPath($this->_rootModules);
        $this->_component->setControllerModule($this->_moduleController, $this->_module);

        $this->_component->setParameters($this->_parameters);
        $this->_component->init();

        /**
         * Остановка рендеринга компонента с отзывом кеширования и рендеринга шаблонов.
         * Интересно для борьбы с ложными параметрами и переполнением файловой системы файлами кеша.
         */
        if ($this->_component->isStop())
        {
            $this->_resultObject = new \Alib\Result();
            return null;
        }
        $this->_resultObject = $this->_component->process();

        if ($key)
        {
            $cache->save($this->_resultObject, $key, array($this->_module));
        }

        before_return:
        $this->_resultObject->setView($this->view);
        $this->_resultObject->setRegistry(Registry::getInstance());

        //Передача результата хелперу
        $this->view->componentResult($this->_resultObject);

        $this->_html = $this->_resultObject->getHtml();
    }
}
