<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 30.12.12
 * Time: 10:44
 *
 * Извлечь из массива:
 * Данные с описанными ключами.
 * Данные за исключением описанных ключей.
 *
 */
namespace Alib\ArrayClass;
class Extract
{
    protected $_array = [];

    public function __construct(array $array)
    {
        $this->_array = $array;
    }

    /**
     * Выбрать из масива подмассив с указанными ключами.
     *
     * Ключи могут быть переданы как в массиве так и в строке.
     * Разделитель в этом случае - запятая.
     *
     * @param array|string $keys
     * @param bool $null_if_count_zero
     * @return array|null
     */
    public function getWithKeys($keys, $null_if_count_zero = false)
    {
        if (!is_array($keys))
            $keys = explode(',', $keys);
        $result = [];
        foreach($keys as $value)
        {
            $value = trim($value);
            if(array_key_exists($value, $this->_array))
            {
                $result[$value] = $this->_array[$value];
            }
        }
        if ($null_if_count_zero and !count($result))
        {
            return null;
        }
        return $result;
    }
}
