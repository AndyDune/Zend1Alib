<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 12.05.12
 * Time: 15:33
 *
 * Универсальный класс результата. Может хранить в себе массив и html.
 * Важен для кеширования информации от компонента.
 * Сохраняет в себе подключаемые файлы css и js модуле й.
 * Сохраняет загрузки папок модулей.
 *
 * К примеру, компонент возвращает не только текст для вставки на страницу, но и данные для формирования крошек, заговка страницы.
 *
 *
 */
namespace Alib;
class Result implements \ArrayAccess
{
    protected $_html = '';

    protected $_view = '';

    protected $_array = array();

    protected $_css = array();
    protected $_js = array();
    protected $_moduleHelper = array();

    protected $_registry = null;

    public function __construct()
    {
        //$this->_registry = Registry::getInstance(); // Объект класса кешируется - не надо кешировать и view
        //$this->_view = $this->_registry->get('view'); //

    }

    public function addCss($file, $module = null, $fromEnd = false)
    {
        if (!$module)
        {
            $module = $this->_registry->get('module');
        }
        $module = strtolower($module);

        $this->_css[] = array($file, $module, $fromEnd);
        return $this;
    }

    public function addJs($file, $module = null)
    {
        if (!$module)
        {
            $module = $this->_registry->get('module');
        }
        $module = strtolower($module);

        $this->_js[] = array($file, $module);
        return $this;
    }

    public function addModuleHelperPath($module)
    {
        $this->_moduleHelper[] = $module;
        return $this;
    }


    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->_array[] = $value;
        }
        else
        {
            $this->_array[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_array[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_array[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_array[$offset]) ? $this->_array[$offset] : null;
    }


    public function setArray($array)
    {
        $this->_array = $array;
        return $this;
    }

    public function setView($view)
    {
        $this->_view = $view;
        return $this;
    }

    public function setRegistry($registry)
    {
        $this->_registry = $registry;
        return $this;
    }



    public function getArray()
    {
        return $this->_array;
    }


    /**
     * Копирование данных из переданного объекта.
     *
     * @param Result $result
     * @return Result
     */
    public function copyFrom(Result $result)
    {
        $this->_html  = $result->getHtml();
        $this->_array = $result->getArray();
        return $this;
    }


    public function setHtml($html)
    {
        $this->_html = $html;
        return $this;
    }

    public function getHtml()
    {
        $viewFiles = $this->_view->viewFiles();


        foreach ($this->_moduleHelper as $module)
        {
            $this->_view->addModuleHelperPath($module);
        }

        foreach ($this->_css as $css)
        {
            $viewFiles->css($css[0], $css[1], $css[2]);
        }

        foreach ($this->_js as $js)
        {
            $viewFiles->js($js[0], $js[1]);
        }


        return $this->_html;
    }


    public function __toString()
    {
        return $this->getHtml();
    }
}
