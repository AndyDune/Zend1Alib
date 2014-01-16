<?php
/**
 *
 *
 * User: Dune
 * Date: 11.03.12
 * Time: 23:40
 */
namespace Alib\View\Helper;
use Alib;
class Structure extends \Alib\View\HelperAbstract
{
    /**
     * @var Alib\Structure
     */
    protected $_structure;

    /**
     * @var string
     */
    protected $_separator = ' - ';

    public function direct($config = null)
    {
        $this->_structure = Alib\Structure::getInstance();
        return $this;
    }

    public function echoTitle()
    {
        ?><title><?= $this->_generateTitle() ?></title><?php
        return $this;
    }

    /**
     * Генерация заголовка страинцы
     *
     * TODO Изменять порядок сбора строки
     * TODO Изменять разделитель. Выделять основную часть (название сайта).
     *
     * @return string
     */
    public function _generateTitle()
    {
        $result = '';
        $separator = '';
        $objects = $this->_structure->getLevelObjects();
        if (!count($objects))
            goto ex;
        foreach($objects as $value)
        {
            /**
             * @var $value \Alib\Structure\Level
             */
            $title = $value->getLevelIndex()->title;
            if (!$title)
                continue;

            $result = $title . $separator . $result;
            $separator = $this->_separator;
        }
        ex:
        $accumulator = Alib\Accumulator::getInstance()->getTitle();
        foreach($accumulator as $value)
        {
            $result = $value . $separator . $result;
            $separator = $this->_separator;

        }
        return $result;
    }

    public function echoKeywords()
    {
        return $this;
    }

    protected function echoDescription()
    {
        return $this;
    }

}