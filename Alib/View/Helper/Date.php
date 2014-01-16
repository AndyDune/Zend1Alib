<?php
/* 
 * V.05
 * 
 * Отображение даты
 *
 * Доработки:
 *
 * 2011-09-29 
 *      Вывод даты тип 12
 * 
 * 2011-03-10
 *      Парсинг строки времени DATETIME стал более устойчивым.
 *      Педнули не оябязательны. Время не обязательно, как целиком, так и частями.
 *      Метод: _collectTimeBitsFromDatetime
 *
 * 2011-03-09 Формат 3: Среда, 9 марта
 *
 * 2011-03-02
 *  Введен вывод данных в 2-х форматах: время до даты и после.
 *  Проверка на ввод существующего формата.
 *
 */
namespace Alib\View\Helper;
use Alib;
use Alib\View;
class Date extends View\HelperAbstract implements View\Helper\InterfaceClass\TableRowDecorator
{
    protected $_data = '';
    protected $_dataParts = '';

    protected $_formats = array(1, 2, 3, 4, 11, 7, 12);

    protected $_format = 1;
    
    public function direct($date = null, $format = 1)
    {
        if ($date === null)
            $date = time();

        if (strpos($date, '-'))
            $this->_dataParts = $data = $this->_collectTimeBitsFromDatetime($date);
        else
            $this->_dataParts = $data = $this->_collectTimeBitsFromUnixdate($date);

        $metod_name = '_format' . $format;
        $this->$metod_name($data);

        return $this;
    }
    
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }
    
    public function setData($data)
    {
        return $this;
    }

    public function setValue($date)
    {
        if ($date === null)
            $date = time();

        if (strpos($date, '-'))
            $this->_dataParts = $data = $this->_collectTimeBitsFromDatetime($date);
        else
            $this->_dataParts = $data = $this->_collectTimeBitsFromUnixdate($date);

        $metod_name = '_format' . $this->_format;
        $this->$metod_name($data);

        return $this;
    }
    

    protected function _format1($data)
    {
        $this->_data = $data['hours']
                     . ':'
                     . $data['minutes_with_zero']
                     . ', '
                     . $data['mday']
                     . ' '
                     . strtoupper($this->getMonthName($data['mon'], 2))
                     . ' '
                     . $data['year'];
    }

    protected function _format12($data)
    {
        $this->_data = $data['hours']
                     . ':'
                     . $data['minutes_with_zero']
                     . ' '
                     . $data['mday']
                     . '.'
                     . $data['mon']
                     . '.'
                     . $data['year'];
    }


    protected function _format10($data)
    {
        $this->_data =
              $data['mday']
            . '.'
            . $data['mon']
            . '.'
            . $data['year']
            . ' '
            . $data['hours']
            . ':'
            . $data['minutes_with_zero']
        ;
    }

    
    protected function _format11($data)
    {
        $this->_data = $data['hours']
                     . ':'
                     . $data['minutes_with_zero']
                     . ', '
                     . $data['mday']
                     . ' '
                     . $this->getMonthName($data['mon'], 2)
                     . ' '
                     . $data['year'];
    }

    protected function _format7($data)
    {
        $this->_data = $data['mday']
                     . ' '
                     . $this->getMonthName($data['mon'], 2)
                     . ' '
                     . $data['year'];
    }



    protected function _format2($data)
    {
        $this->_data = $data['mday']
                     . ' '
                     . strtoupper($this->getMonthName($data['mon'], 2))
                     . ' '
                     . $data['year']
                     . ', '
                     . $data['hours']
                     . ':'
                     . $data['minutes_with_zero']
            ;
    }


    protected function _format4($data)
    {
        $this->_data = $data['mday']
                     . '.'
                     . $data['mon_with_zero']
                     . '.'
                     . $data['year']
            ;
    }


    protected function _format3($data)
    {
        $str = \Dune_String_Factory::getStringContainer($this->getWeekDayName($data['weekday'], 1), true);
        $this->_data = $str->ucfirst()
                     . ', '
                     . $data['mday']
                     . ' '
                     . $this->getMonthName($data['mon'], 2)
            ;
    }


    protected function _format5($data)
    {
        $this->_data = $data['mday']
                     . ' '
                     . strtoupper($this->getMonthName($data['mon'], 2))
                     . ' '
                     . $data['year']
            ;
    }

    protected function _format6($data)
    {
        $this->_data = $data['mday']
                     . ' '
                     . $this->getMonthName($data['mon'], 2)
            ;
    }

    protected function _format8($data)
    {
        $this->_data = $data['mday']
                     . '.'
                     . $data['mon_with_zero']
            ;
    }

    
    protected function _format9($data)
    {
        $this->_data = $this->getMonthName($data['mon'], 1)
                     . ' '
                     . $data['year']
            ;
    }
    


            /**
     * Наименование месяца с формами.
     *
     * @param integer $number порядковый номер месяца
     * @param integer $format тип
     * @return string
     */
    public function getMonthName($number = null, $format = 1)
    {
        if ($number === null)
        {
            $number = $this->_dataParts['mon'];
        }

        $monthName = array(
                        0  => array(1 => 'месяц',    'месяца',   'месяце',  'month'),
                        1  => array(1 => 'январь',   'января',   'январе',  'january'),
                        2  => array(1 => 'февраль',  'февраля',  'феврале', 'february'),
                        3  => array(1 => 'март',     'марта',    'марте',   'march'),
                        4  => array(1 => 'апрель',   'апреля',  'апреле',   'april'),
                        5  => array(1 => 'май',      'мая',      'мае',     'may'),
                        6  => array(1 => 'июнь',     'июня',     'июне',    'june'),
                        7  => array(1 => 'июль',     'июля',     'июле',    'july'),
                        8  => array(1 => 'август',   'августа',  'августе', 'august'),
                        9  => array(1 => 'сентябрь', 'сентября', 'сентябре','september'),
                        10 => array(1 => 'октябрь',  'октября',  'октябре', 'october'),
                        11 => array(1 => 'ноябрь',   'ноября',   'ноябре',  'november'),
                        12 => array(1 => 'декабрь',  'декабря',  'декабре', 'december'),
                    );


            $number = (int)$number;
            if (isset($monthName[$number]))
            {
                return $monthName[$number][$format];
            }
            else
            {
                return $monthName[0][$format];
            }
    }

    public function getWeekDayName($number = null, $format = 1)
    {
        if ($number === null)
        {
            $number = $this->_dataParts['weekday'];
        }

        $dayName = array(
                        0  => array(1 => 'день',    'дня',   'дне',  'day'),
                        1  => array(1 => 'понедельник',   'января',   'январе',  'january'),
                        2  => array(1 => 'вторник',  'февраля',  'феврале', 'february'),
                        3  => array(1 => 'среда',     'марта',    'марте',   'march'),
                        4  => array(1 => 'четверг',   'апреля',  'апреле',   'april'),
                        5  => array(1 => 'пятница',      'мая',      'мае',     'may'),
                        6  => array(1 => 'суббота',     'июня',     'июне',    'june'),
                        7  => array(1 => 'воскресение',     'июля',     'июле',    'july'),
                    );


            $number = (int)$number;
            if (isset($dayName[$number]))
            {
                return $dayName[$number][$format];
            }
            else
            {
                return $dayName[0][$format];
            }
    }


    /**
     * Номер месяца
     *
     * @return integer
     */
    public function getMonth()
    {
        return $this->_dataParts['mon'];
    }

    /**
     * Номер месяца
     *
     * @return integer
     */
    public function getMonthZero()
    {
        return $this->_dataParts['mon_with_zero'];
    }


    public function get()
    {
        return $this->_data;
    }


    /**
     * День
     *
     * @return integer
     */
    public function getDay()
    {
        return $this->_dataParts['mday'];
    }

    /**
     * Год
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->_dataParts['year'];
    }

    protected function _collectTimeBitsFromDatetime($string)
    {
        $str = \Dune_String_Factory::getStringContainer($string, 1);
        $array = array();

        $times = explode(' ', $string);
        
        if (isset($times[0]))
        {
            $date = explode('-', $times[0]);
            $array['year']  = (int)$date[0];
            $array['mon']   = (int)$date[1];
            $array['mon_with_zero']   = $date[1];
            $array['mday']  = (int)$date[2];
        }

       $array['hours']    = 0;
       $array['minutes']  = 0;
       $array['minutes_with_zero']  = '00';
       $array['seconds']  = 0;


        if (isset($times[1]))
        {
            $date = explode(':', $times[1]);
            $array['hours']    = (int)$date[0];
            if (isset($date[1]))
            {
                $array['minutes']  = (int)$date[1];
                $array['minutes_with_zero']  = $date[1];
            }
            if (isset($date[2]))
            {
                $array['seconds']  = (int)$date[2];
            }
        }

/*
        $array['year']  = (int)$str->substr(0, 4);
        $array['mon']   = (int)$str->substr(5, 2);
        $array['mon_with_zero']   = $str->substr(5, 2);
        $array['mday']  = (int)$str->substr(8, 2);
*/

        $time = $this->_makeTimestampFromTimeBits($array);

        $array['weekday']  = date('N', $time);
/*
        $array['hours']    = (int)$str->substr(11, 2);
        $array['minutes']  = (int)$str->substr(14, 2);
        $array['minutes_with_zero']  = $str->substr(14, 2);
        $array['seconds']  = (int)$str->substr(17, 2);
  */
        return $array;
    }

    protected function _collectTimeBitsFromUnixdate($number)
    {
        $array['year']  = date('Y', $number);
        $array['mon']   = date('n', $number);
        $array['mday']  = date('j', $number);

       $array['mon_with_zero']   = date('m', $number);;

        $array['weekday']  = date('N', $number);

        $array['hours']    = (int)date('H', $number);
        $array['minutes']  = (int)date('i', $number);
        $array['minutes_with_zero']  = date('i', $number);
        $array['seconds']  = (int)date('s', $number);
        return $array;
    }

    protected function _makeTimestampFromTimeBits($array)
    {
        $result = mktime($array['hours'],
                         $array['minutes'],
                         $array['seconds'],
                         $array['mon'],
                         $array['mday'],
                         $array['year']);
        return $result;
    }


    public function __toString()
    {
        return $this->_data;
    }


}