<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 16.10.12
 * Time: 15:02
 *
 * Извлечение информации о дате и времени из строки.
 * Строка содержит человекопонятный формат даты.
 * Информация может быть не полной.
 *
 */
namespace Alib\Date;
class ExtractFromString
{
    protected $_incomeString = '';

    public function __construct($string)
    {
        $this->_incomeString = $string;
    }
}
