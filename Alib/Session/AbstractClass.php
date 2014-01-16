<?php
/*
 * Доступ к зоне сессии, хранящей сообщения
 */
namespace Alib\Session;
abstract class AbstractClass extends \Zend_Session_Namespace
{

    /**
     * Очистка зоны сессии, всей или одного ключа или ключа в массиве,
     * которым является указанное значение.
     *
     * @param string|null $name_space
     * @param array|null $exept
     * @return boolean
     */
    public function clean($name_space = null, $exept = null)
    {
        // Удаляем всё
        if ($name_space === null and $exept === null)
        {
            foreach($this as $key => $value)
            {
                unset($this->$key);
            }
            return true;
        }

        // Удаляем отдельные значения, исключая перечисленные в $exept
        if ($name_space === null)
        {
            foreach($this as $key => $value)
            {
                if (!in_array($key, $exept))
                    unset($this->$key);
            }
            return true;
        }

        // Удаляем отдельныое значеи
        if ($exept === null)
        {
            unset($this->$name_space);
            return true;
        }

        // Первый уровень содержит массив - пекребираем его значения и удалем все,
        // исключая перечисленные в $exept
        if (is_array($this->$name_space))
        {

            $array = $this->$name_space;
            foreach($array as $key => $value)
            {
                if (!in_array($key, $exept))
                {
                    unset($array[$key]);
                }
            }
            $this->$name_space = $array;
        }
        return true;
    }
}

