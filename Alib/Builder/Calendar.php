<?php

/**
 * Builds and manipulates an events calendar
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to the MIT License, available
 * at http://www.opensource.org/licenses/mit-license.html
 *
 * @author     Jason Lengstorf <jason.lengstorf@ennuidesign.com>
 * @copyright  2009 Ennui Design
 * @license    http://www.opensource.org/licenses/mit-license.html
 */
namespace Alib\Builder;
class Calendar
{

    /**
     * The date from which the calendar should be built
     *
     * Stored in YYYY-MM-DD HH:MM:SS format
     *
     * @var string the date to use for the calendar
     */
    private $_useDate;

    /**
     * The month for which the calendar is being built
     *
     * @var int the month being used
     */
    private $_m;

    /**
     * The year from which the month's start day is selected
     *
     * @var int the year being used
     */
    private $_y;

    /**
     * The number of days in the month being used
     *
     * @var int the number of days in the month
     */
    private $_daysInMonth;

    /**
     * The index of the day of the week the month starts on (0-6)
     *
     * @var int the day of the week the month starts on
     */
    private $_startDay;
    
    
    protected $_userDecorator = false;
    /**
     * @var CalendarDecorator
     */
    protected $_decorator = null;
    
    

   /**
     * Creates a database object and stores relevant data
     *
     * Upon instantiation, this class accepts a database object
     * that, if not null, is stored in the object's private $_db
     * property. If null, a new PDO object is created and stored
     * instead.
     *
     * Additional info is gathered and stored in this method,
     * including the month from which the calendar is to be built,
     * how many days are in said month, what day the month starts
     * on, and what day it is currently.
     *
     * @param string $useDate the date to use to build the calendar
     * @return void
     */
    public function __construct($useDate=NULL)
    {

        /*
         * Gather and store data relevant to the month
         */
        if ( isset($useDate) )
        {
             $this->_useDate = $useDate;
        }
        else
        {
             $this->_useDate = date('Y-m-d H:i:s');
        }

        /*
         * Convert to a timestamp, then determine the month
         * and year to use when building the calendar
         */
        $ts = strtotime($this->_useDate);
        $this->_m = date('m', $ts);
        $this->_y = date('Y', $ts);

        /*
         * Determine how many days are in the month
         */
        $this->_daysInMonth = cal_days_in_month(
                CAL_GREGORIAN,
                $this->_m,
                $this->_y
            );

        /*
         * Determine what weekday the month starts on
         */
        $ts = mktime(0, 0, 0, $this->_m, 1, $this->_y);
        $this->_startDay = date('N', $ts);
    }

    
    public function setDecorator($decorator)
    {
        $this->_userDecorator = true;
        $this->_decorator = $decorator;
        return $this;
    }


    /**
     * Returns HTML markup to display the calendar and events
     *
     * Using the information stored in class properties, the
     * events for the given month are loaded, the calendar is
     * generated, and the whole thing is returned as valid markup.
     *
     * @return string the calendar HTML markup
     */
    public function build()
    {
        /*
         * Determine the calendar month and create an array of
         * weekday abbreviations to label the calendar columns
         */
        $cal_month = date('F Y', strtotime($this->_useDate));
        $weekdays = array('Mon', 'Tue',
                'Wed', 'Thu', 'Fri', 'Sat', 'Sun'   );

        $html = '';
        /*
         * Add a header to the calendar markup
         */
        /*
        $html = "<h2>$cal_month</h2>";
        for ( $d=0, $labels=NULL; $d<7; ++$d )
        {
            $labels .= "<li>" . $weekdays[$d] . "</li>";
        }
        $html .= '<ul class="weekdays">'
            . $labels . "</ul>";
        */
        /*
         * Load events data
         */
        $events = array();

        /*
         * Create the calendar markup
         */
        $html .= "<ul>"; // Start a new unordered list
        for ( $i=1, $c=1, $t=date('j'), $m=date('m'), $y=date('Y');
                $c<=$this->_daysInMonth; ++$i )
        {
            /*
             * Apply a "fill" class to the boxes occurring before
             * the first of the month
             */
            $class = $i < $this->_startDay ? "fill" : NULL;

            /*
             * Add a "today" class if the current date matches
             * the current date
             */
            /*
            if ( $c==$t && $m==$this->_m && $y==$this->_y )
            {
                $class = "today";
            }
             */


            $event_info = NULL; // clear the variable
            /*
             * Add the day of the month to identify the calendar box
             */
            if ( $this->_startDay <= $i && $this->_daysInMonth>=$c)
            {
                /*
                 * Format events data
                 */
                $event_info = NULL; // clear the variable
                if ($this->_userDecorator)
                {
                    $event_info = $this->_decorator->check($c, $this->_m, $this->_daysInMonth)->get();
                    $class = $this->_decorator->getClass();
                }

                $date = '';
                
                $c++;
            }
            else { $date="&nbsp;"; }

            /*
             * Build the opening and closing list item tags
             */
            if ($class)
                $ls = sprintf('<li class="%s">', $class);
            else
                $ls = '<li>';
            $le = '</li>';
            
            
            /*
             * If the current day is a Saturday, wrap to the next row
             */
            $wrap = $i!=0 && $i%7==0 ? "</ul><ul>" : NULL;

            /*
             * Assemble the pieces into a finished item
             */
            $html .= $ls . $date . $event_info . $le . $wrap;
        }

        /*
         * Add filler to finish out the last week
         */
        while ( $i%7!=1 )
        {
            $html .= '<li class="fill">&nbsp;</li>';
            ++$i;
        }

        /*
         * Close the final unordered list
         */
        $html .= "</ul>";

        /*
         * Return the markup for output
         */
        return $html;
    }
   
}

