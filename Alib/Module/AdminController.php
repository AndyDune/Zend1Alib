<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 22.10.12
 * Time: 14:38
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Module;
class AdminController
{
    use \Alib\Controller\Traits\DataToView;
    use \Alib\System\Traits\BuildClassName;

    /**
     * @var \Application\Admin\Library\Controller\Action
     */
    protected $_adminController;

    protected $view;
    protected $_renderLocalView = true;

    protected $_viewScript = '';

    protected $_module = null;

    protected $_helper = null;

    public function __construct($controller)
    {
        $this->_adminController = $controller;
        $this->_helper = $this->_adminController->getHelperBroker();
        $this->view = $controller->view;
    }

    final public function init()
    {
        $this->_init();
    }

    protected function _init()
    {

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



    /**
     * @param $name
     * @param $group
     * @param string $realization
     * @param string $module
     * @return \Alib\Record\AbstractClass\Record
     */
    public function getRecord($name, $group, $realization = 'base', $module = null)
    {
        if (!$module)
            $module = $this->getModuleName();
        return $this->getController()->getRecord($name, $group, $realization, $module);
    }


    protected function _processSaveRecord(\Alib\Record\AbstractClass\Record $record)
    {
        return $this->getController()->processSaveRecord($record);
    }

    /**
     * @param \Alib\Record\AbstractClass\Record $record
     * @return bool Возврат false если
     */
    protected function _checkRecordId(\Alib\Record\AbstractClass\Record $record)
    {
        return $this->getController()->checkRecordId($record);
    }

    /**
     * @return \Application\Admin\Library\Controller\Action
     */
    public function getController()
    {
        return $this->_adminController;
    }


    public function getViewScriptName()
    {
        return $this->_viewScript;
    }

    protected function _setView($view = null)
    {
        if (!$view)
            $this->_renderLocalView = false;
        else
            $this->_renderLocalView = true;

        $this->_viewScript = $view;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRenderLocalView()
    {
        return $this->_renderLocalView;
    }

    public function extractPageParam()
    {
        $page = (int)$this->getParam('page', 1);
        if ($page < 1)
            $page = 1;
        return $page;
    }

    public function getParam($paramName, $default = null)
    {
        return $this->getController()->getParam($paramName, $default);
    }

    protected function _getParam($paramName, $default = null)
    {
        return $this->getController()->getParam($paramName, $default);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->_adminController, $name], $arguments);
    }

    public function _redirectIfNotAjax($url = null, $formdata = null)
    {
        $this->_setView();
        $this->getController()->redirectIfNotAjax($url, $formdata);
    }

    public function _redirectToUrlIfNotAjax($url = null, $formdata = null)
    {
        $this->_setView();
        $this->getController()->redirectToUrlIfNotAjax($url, $formdata);
    }

}
