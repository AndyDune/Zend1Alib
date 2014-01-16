<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 30.10.12
 * Time: 14:18
 * To change this template use File | Settings | File Templates.
 */

namespace Alib\View\Helper;
use Alib;
class GetValueFromRequest extends \Alib\View\HelperAbstract
{
    public function direct($name = null, $array = null)
    {
        if ($array)
        {
            if (isset($_GET[$array][$name]))
                return $_GET[$array][$name];
        }
        else
        {
            $request = \Alib\Request::getInstance();
            $get = $request->getGetConteiner();
            return $get->{$name};
        }
        return null;
    }

}
