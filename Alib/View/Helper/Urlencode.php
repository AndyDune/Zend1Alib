<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 17.04.12
 * Time: 16:56
 */

namespace Alib\View\Helper;
class Urlencode extends \Alib\View\HelperAbstract
{
    public function direct($string = null)
    {
        $this->_html = urlencode($string);
        return $this;
    }
}
