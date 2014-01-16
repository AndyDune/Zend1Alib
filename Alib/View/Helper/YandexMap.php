<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 03.10.12
 * Time: 14:03
 *
 * Вспомогательные методы по работы с Яндекс картами.
 *
 */
namespace Alib\View\Helper;
use Alib;
class YandexMap extends \Alib\View\HelperAbstract
{
    protected $_line = '<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>';
    public function direct($value = null)
    {
        $this->_html = $this->_line;
        return $this;
    }
}