<?php
/**
 * V.03
 * 
 * Фильтр данных дата формирует строку для вставки в базу данных
 * 
 * История:
 *    2011-09-30 Испоавлено ряд ошибок.
 *               ! Код требует рефакторинга.
 *    2011-09-01 Доработана ошибка генерации времени
 * 
 */
namespace Alib\Filter;
use Alib\Form;
class DateTime implements \Zend_Filter_Interface
{
    protected $_builder      = null;
    protected $_formField      = '';

    /**
     *
     * @return void
     */
    public function __construct($options = array())
    {
        
    }

    public function setBuilder(Form\Builder $builder)
    {
        $this->_builder = $builder;
        return $this;
    }

    public function setFormField($value)
    {
        $this->_formField = $value;
        return $this;
    }
    
    protected function _scheckDatePos($array)
    {
        $result = array();
        try
        {
            if (count(explode('.', $array[1])) > 1)
            {
                $result[0] = $array[1];
                $result[1] = $array[0];
                throw new Exception('Передвижено', 1);
            }

            if (count(explode('/', $array[1])) > 1)
            {
                $result[0] = $array[1];
                $result[1] = $array[0];
                throw new Exception('Передвижено', 1);
            }

            if (count(explode(':', $array[0])) > 1)
            {
                $result[0] = $array[1];
                $result[1] = $array[0];
                throw new Exception('Передвижено', 1);
            }
            
            if (count(explode('-', $array[1])) > 1)
            {
                $result[0] = $array[1];
                $result[1] = $array[0];
                throw new Exception('Передвижено', 1);
            }
            $result = $array;
        }
        catch (Exception $e)
        {
            
        }
        

        if ((int)$result[1] > 31 and (int)$result[1] < 60)
        {
            $result[1] = date('H') . ':' . (int)$result[1];
        }
        
        return $result;
    }

    /**
     * 
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {
        $result_time_minute = date('i');
        $result_time_houre  = date('H');
        
        $value = trim($value);
        if (!$value)
            return date('Y-m-d H:i:s');
        $parts = explode(' ', $value);
        
        $mega_count = count($parts);
        if ($mega_count > 2)
        {
            $parts_temp = array();
            $count = 0;
            foreach($parts as $value)
            {
                if ($count > 1)
                    break;
                if ($value)
                {
                    $count++;
                    $parts_temp[] = $value;
                }
            }
            $parts = $parts_temp;
            $parts = $this->_scheckDatePos($parts);
        }
        else if ($mega_count == 1)
        {
            $result_time = $result_time_houre . ':' . $result_time_minute;
        }
        else
        {
            $parts = $this->_scheckDatePos($parts);
        }
        
        if (!isset($result_time))
        {
            $parts[1] = preg_replace('|[^0-9.,:]|u', '', $parts[1]);
            $time_parts = explode('.', $parts[1]);
            if (count($time_parts) < 2)
            {
                $time_parts = explode(':', $parts[1]);
            }
            if (count($time_parts) < 2)
            {
                $time_parts = explode(',', $parts[1]);
            }
            
            $h = (int)$time_parts[0];
            if ($h > 23 or $h < 0)
            {
                $h = 0;
            }
            if (isset($time_parts[1]))
            {
                $m = (int)$time_parts[1];
                if ($m > 59 or $m < 0)
                {
                    $m = 0;
                }
                
            }
            else
                $m = 0;
            $result_time = $h . ':' . $m;
        }
        
        
        $date_part = preg_replace('|[^-0-9.,/_]|u', '', $parts[0]);
        
        $del = '-';
        $date_parts = explode($del, $date_part);
        if (count($date_parts) < 2)
        {
            $del = '.';
            $date_parts = explode('.', $date_part);
        }
        if (count($date_parts) < 2)
        {
            $del = '_';
            $date_parts = explode('_', $date_part);
        }
        
        if (count($date_parts) < 2)
        {
            $del = ',';
            $date_parts = explode(',', $date_part);
        }
        
        if (count($date_parts) < 2)
        {
            $del = '/';
            $date_parts = explode('/', $date_part);
        }
        if (count($date_parts) < 2)
        {
            $del = '';
        }
        
        $count_date_infos = count($date_parts);
        
        $calendar = new \Dune_Time_Calendar();
        // Вычисляем с какой стороны год
        if (!$del)
        {
            $year = date('Y');
            $month = date('m');
            $max_day = $calendar->daysInMonth((int)$month, (int)$year);
            $day = (int)$date_parts[0];
            if ($day < 1 or $day > $max_day)
                $day = 1;
        }
        // День и месяц
        else if ($count_date_infos == 2)
        {
            $year = date('Y');
            $month = (int)$date_parts[1];
            if ($month < 1 or $month > 12)
            {
                $month = date('m');
            }
            $max_day = $calendar->daysInMonth((int)$month, (int)$year);
            $day = (int)$date_parts[0];
            if ($day < 1 or $day > $max_day)
                $day = 1;
        }        
        // В начале
        else if (strlen($date_parts[0]) > 2)
        {
            $year = (int)$date_parts[0];
            if ($year < 0 or $year > 9999)
            {
                $year = date('Y');
            }
            $month = (int)$date_parts[1];
            if ($month < 1 or $month > 12)
            {
                $month = date('m');
            }
            $max_day = $calendar->daysInMonth((int)$month, (int)$year);
            $day = (int)$date_parts[2];
            if ($day < 1 or $day > $max_day)
                $day = 1;
        }
        else
        {
            $year = (int)$date_parts[2];
            if ($year < 0 or $year > 9999)
            {
                $year = date('Y');
            }
            $month = (int)$date_parts[1];
            if ($month < 1 or $month > 12)
            {
                $month = date('m');
            }
            $max_day = $calendar->daysInMonth((int)$month, (int)$year);
            $day = (int)$date_parts[0];
            if ($day < 1 or $day > $max_day)
                $day = 1;
        }
        
        return $year . '-' . $month . '-' . $day . ' ' . $result_time;
    }
}