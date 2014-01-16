<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.12.12
 * Time: 16:03
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\View\Helper;
use Alib;
class Ruble extends \Alib\View\HelperAbstract
{

    protected $_ruble = '';
    protected $_first = true;

    protected $_styles = "
    <style>
        @font-face {
            font-family: 'Ruble';
            src: url('/viewfiles/common/rouble/rouble.eot');
            src: local('ALS Ruble'),
            url('/viewfiles/common/rouble/rouble.woff') format('woff'),
            url('/viewfiles/common/rouble/rouble.svg') format('svg'),
            url('/viewfiles/common/rouble/rouble.otf') format('opentype');
        }
    </style>
    ";

    public function direct($mode = null, $params = [])
    {
        $css = '';
        $letter = 'a';
        if (isset($params['letter']))
            $letter = $params['letter'];
        if (isset($params['size']))
            $css = 'font-size:' .  $params['size'] . ';';
        $this->_ruble = '<span class="ruble" style="' . $css . '">' . $letter . '</span>';
        return $this;
    }
    public function getPreparation()
    {
        return $this->_styles;
    }


    protected function _beforeObjectEcho()
    {
        $this->_html = $this->_ruble;
        $this->_first = false;
    }

}
