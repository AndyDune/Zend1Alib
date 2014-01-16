<?php

/**
 * Исключение из массива значений с нулями.
 * Всех по умолчанию или только указанных в опциях.
 */

namespace rzn\lib\www;
class Form_Builder_Filter_NoNull extends Form_Builder_Abstract_Filter
{
    public function filter($data)
    {
        $values = $data;
        $keys = $this->_options;
        
        if ($keys)
        {
            foreach($keys as $key)
            {
                if (key_exists($key, $values) and is_null($values[$key]))
                {
                    unset($values[$key]);
                }
            }
            $result = $values;
        }
        else
        {
            foreach($values as $key => $value)
            {
                if ($value !== null)
                {
                    $result[$key] = $value;
                }
            }
        }
        return $result;        
    }
    
}