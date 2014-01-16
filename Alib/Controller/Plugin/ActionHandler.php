<?php
/**
 * 
 * create 2011-12-26
 * 
 */
namespace Alib\Controller\Plugin;
use Alib\Request;
class ActionHandler extends \Zend_Controller_Plugin_Abstract
{
    /**
     * @var Zend_Controller_Front
     */
    protected $_front;

    public function  __construct()
    {
        $this->_front = \Zend_Controller_Front::getInstance();
    }

    public function preDispatch(\Zend_Controller_Request_Abstract $request)
    {
        $controller       = $request->getControllerName();
        $controllerAction = $request->getActionName();
        
        $url = Request::getInstance();
        $get = $url->getGetConteiner();
        $post = $url->getPostConteiner();
        
        $application_action = $post['application_action'];
        
        if (isset($_POST['application']['controller']) 
            and isset($_POST['application']['action']))
        {
            $request->setControllerName($_POST['application']['controller'])
                    ->setActionName($_POST['application']['action']);
        }
        else if (isset($_GET['application']['controller']) 
                 and isset($_GET['application']['action']))
        {
            $request->setControllerName($_GET['application']['controller'])
                    ->setActionName($_GET['application']['action']);
        }
        
    }
}
