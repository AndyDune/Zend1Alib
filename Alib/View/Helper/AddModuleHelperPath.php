<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 02.07.12
 * Time: 16:05
 *
 *
 *
 */

namespace Alib\View\Helper;
class AddModuleHelperPath extends \Alib\View\HelperAbstract
{
    protected $_modules = array();
    public function direct($module = null)
    {
        $name = ucfirst($module);
        if (array_key_exists($name, $this->_modules))
            goto before_return;

        $reg = \Alib\Registry::getInstance();
        $this->view->addHelperPath($reg->get('dir') . '/modules/' . $name . '/views/helpers', "Application\\" . $name . "\\View\\Helper\\");

        before_return:
        return $this;

    }
}