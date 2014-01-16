<?php
/**
 *
 * Обработка и валидация даты, состоящей из сборных данных.
 */

namespace Alib\Format;
class DateAssembly
{
    protected $_day   = null;
    protected $_month = null;
    protected $_year  = null;


    public function __construct($params = null)
    {
        if ($params)
        {
            if (array_key_exists('day', $params))
                $this->setDay($params['day']);
            if (array_key_exists('month', $params))
                $this->setMonth($params['month']);
            if (array_key_exists('year', $params))
                $this->setYear($params['year']);
        }
    }

    public function setDay($value)
    {
        $value = substr(preg_replace('|[^0-9]|u', '', $value), 0, 2);
        $this->_day = $value;
        return $this;
    }

    public function setMonth($value)
    {
        $value = substr(preg_replace('|[^0-9]|u', '', $value), 0, 2);
        $this->_month = $value;
        return $this;
    }

    public function setYear($value)
    {
        $value = substr(preg_replace('|[^0-9]|u', '', $value), 0, 4);
        $this->_year = $value;
        return $this;
    }

    public function isMonth()
    {
        $month = (int)$this->_month;
        if ($month > 12 or $month < 1)
        {
            return false;
        }
        return true;
    }

    public function isYear()
    {
        $year = (int)$this->_year;
        if ($year > 9999 or $year < 0)
        {
            return false;
        }
        return true;
    }

    public function isDay()
    {
        if (!$this->isMonth() or !$this->isYear())
        {
            return false;
        }

        $day = (int)$this->_day;
        if ($day > 31 or $day < 1)
            return false;
        $dayMax = date('t', strtotime($this->_year. '-' . $this->_month . '-01 00:00'));
        if ($day > $dayMax)
            return false;

        return true;
    }

    public function getDateTime()
    {
        if (!$this->isDay())
            return false;
        return $this->_year. '-' . $this->_month . '-' . $this->_day;
    }


}