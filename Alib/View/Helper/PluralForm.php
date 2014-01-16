<?php
/**
 * Описание колличества с правильным склонением.
 * 1 огурец
 * 2 огурца
 * 10 огурцов

 *
 * @package
 * @category
 * @author      Andrey Ryzhov <webmaster@rzn.info>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 */

namespace Alib\View\Helper;
class PluralForm extends \Alib\View\HelperAbstract
{
    private $_html = '';


    /**
     *
     * @param integer $n рассматриваемое число
     * @param string $form1 огурец
     * @param string $form2 огурца
     * @param string $form3 огурцов
     * @return Www_View_Helper_PluralForm
     */
    public function direct($n = null, $form1 = null, $form2 = null, $form3 = null)
    {
        $this->_html = $this->_pluralForm($n, $form1, $form2, $form3);
        return $this;
    }


    /**
     *
     * @param integer $n рассматриваемое число
     * @param string $form1 огурец
     * @param string $form2 огурца
     * @param string $form3 огурцов
     * @return string нужный вариант
     */
    protected function _pluralForm($n, $form1, $form2, $form3)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $form3;
        if ($n1 > 1 && $n1 < 5) return $form2;
        if ($n1 == 1) return $form1;
        return $form3;
    }

    public function  get()
    {
        return $this->_html;
    }


    public function  __toString()
    {
        return $this->get();
    }
}
