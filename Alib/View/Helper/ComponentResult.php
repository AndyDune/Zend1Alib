<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 14.05.12
 * Time: 11:26
 *
 * Хранение результатов работы компонента.
 */
namespace Alib\View\Helper;
use Alib;
class ComponentResult extends \Alib\View\HelperAbstract
{
    /**
     * @var Alib\Result
     */
    protected $_result = null;

    public function __construct()
    {
        $this->_result = new Alib\Result();
    }

    public function direct(Alib\Result $value = null)
    {
        if ($value)
        {
            $this->_result = $value;
            $this->_html = $this->_result->getHtml();
        }
        return $this;
    }

    protected function pr()
    {
        echo Alib\Test::pr($this->_result);
    }

    public function __get($key)
    {
        return $this->_result[$key];
    }

    public function html()
    {
        return $this->_result->getHtml();
    }


}