<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 16.10.12
 * Time: 15:17
 *
 * Формат данных дата.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class Date extends Record\DataType
{
    public function _processReady()
    {
        $result = true;
        // todo Добавить расширенную обработку даты
        if (!$this->_value)
        {
            $this->_value = null;
            return $result;
        }

        //$valie =

        return $result;
    }


    protected function _formatDateFor($data, $def = null)
    {
        $data = trim($data);
        if (!$data)
            return $def;
        $data = str_replace(array('-', ','), '.', $data);
        $dots = explode('.', $data);
        if (!isset($dots[1]))
            $dots[1] = date('m');
        if (!isset($dots[2]))
            $dots[2] = date('Y');

        $date = new \Dune_Data_Parsing_Date($dots[2] . '-' . $dots[1] . '-01');
        $dayEnd = date('t', $date->getTimeStamp());
        if ($dots[0] > $dayEnd)
        {
            $dots[0] = $dayEnd;
        }


        $dots = array_reverse($dots, true);
        return implode('-', $dots);
    }

    protected function _formatTimeFor($data, $def = null)
    {
        $data = trim($data);
        if (!$data)
            return $def;
        $data = str_replace(array('-', ',', '.'), ':', $data);
        $dots = explode(':', $data);
        if (!isset($dots[1]))
            $dots[1] = '00';
        if ($dots[0] > 23)
            $dots[0] = 7;
        if ($dots[1] > 59)
            $dots[1] = 0;

        return implode(':', $dots);
    }


}

