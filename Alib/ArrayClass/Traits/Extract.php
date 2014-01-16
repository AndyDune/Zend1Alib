<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 16.10.12
 * Time: 11:45
 *
 * Извлечение из массива вусякого.
 */
namespace Alib\ArrayClass\Traits;

trait Extract
{
    public function getValuesFromFieldWithArrayList(array $array, $field)
    {
        $result = [];
        foreach($array as $value)
        {
            $result[] = $value[$field];
        }
        return $result;
    }
}
