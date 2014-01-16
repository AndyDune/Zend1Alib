<?php

/**
 * 
 */
namespace Alib\Builder;
use Application\Www\Model\Data;

class CalendarDecorator
{
    protected $_currentDay;
    protected $_currentMonth;
    
    protected $_result = '';
    protected $_class = '';
    
    
    public function __construct()
    {
        $this->_currentDay   = date('d');
        $this->_currentMonth = date('m');
    }

        /**
     *
     * @param integer $day день
     * @param integer $month месяц
     * @param integer|null $days_in_month количесво дей в месяце - важно для февраля
     * @return TestDecorator 
     */
    public function check($day, $month, $days_in_month = null)
    {
        $this->_result = $day;
        $this->_class = '';
        if ($this->_currentDay == $day and $month == $this->_currentDay)
        {
            $this->_result = $day;
            $this->_class = 'today'; 
        }
        return $this; 
    }
    
    public function get()
    {
        return $this->_result;
    }
    
    public function getClass()
    {
        return $this->_class;
    }
        
}