<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 14.05.12
 * Time: 11:16
 *
 * Тестовя печать данных.
 */
//namespace Application\Www\View\Helper;
namespace Alib\View\Helper;
use Alib;
class Pr extends \Alib\View\HelperAbstract
{
    public function direct($value = null)
    {
        ob_start();
        Alib\Test::pr($value);
        $this->_html = ob_get_clean();
        return $this;
    }
}
