<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 01.10.12
 * Time: 15:17
 *
 * Общий тип данных.
 * Этот тпп используется для полей для которых не указан тип явно.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;

class Common extends Record\DataType
{
    /**
     * Возвращает индикатор, что тип даннх не указан явно.
     *
     * @return bool
     */
    public function is()
    {
        return false;
    }

    public function __call($method = null, $params = null)
    {
        throw new \Alib\Exception('Нет такого метода для обработки этого типа данных (Common)', 1);
    }
}
