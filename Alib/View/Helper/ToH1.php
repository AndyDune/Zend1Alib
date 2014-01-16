<?php
/**
 * Отправить данные в заголовок страницы
 *
 *
 * @author dune
 */
namespace Alib\View\Helper;
use Alib;
class ToH1 extends \Alib\View\HelperAbstract
{
    public function toH1($data = null)
    {
        $h1 = '';
        if ($data)
        {
            $h1 = $data;
        }
        $this->_html = $h1;
        \Alib\Accumulator::getInstance()->setH1($h1);
        return $this;
    }
    
}
