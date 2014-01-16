<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 02.11.12
 * Time: 14:29
 *
 * Удаляет все пробельные символы из строки.
 *
 */
namespace Alib\Record\Filter;
class NoWhiteSpace extends \Alib\Record\AbstractClass\Filter
{
    public function filter($value)
    {
        return preg_replace('|\s|ui', '', $value);
    }
}
