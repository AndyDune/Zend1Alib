<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.04.12
 * Time: 13:41
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Controller\Traits;
trait DataToView
{
    protected function _assignArrayToView($key, $value)
    {
        if ($value and is_array($value))
        {
            $this->view->assign($key, $value);
        }
    }
}
