<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 04.06.12
 * Time: 18:31
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Controller\Plugin;
use Alib\Request;
class Access extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    public function  __construct()
    {
        //$this->_front = \Zend_Controller_Front::getInstance();
    }

    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $auth = \Alib\Auth::getInstance();
        $auth = $auth->getIdentity();
        if (!$auth or !isset($auth['status']) or $auth['status'] < 1000)
        {
            $request->setControllerName('access');
            $request->setActionName('no');
            $request->setModuleName('Admin');
        }
        else
        {

            /**
             * Определение контроллера и действия происходит параметрами из GET
             */
            if (isset($_GET[$request->getControllerKey()]))
            {
                $request->setControllerName(null);
                $request->setParam($request->getControllerKey(), $_GET[$request->getControllerKey()]);
            }
            $request->setActionName(null);
        }

    }
}
