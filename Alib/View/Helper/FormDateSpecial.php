<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 22.11.12
 * Time: 18:54
 *
 *
 *
 */
namespace Alib\View\Helper;
use Alib;
class FormDateSpecial extends \Alib\View\HelperAbstract
{
    protected $_mode = 'date';

    protected $_value = null;

    public function direct($name = null, $value = null, $params = null)
    {
        $this->_value = $value;
        if (!is_array($params))
            goto next;
        if (array_key_exists('mode', $params))
        {
            $this->_mode = $params['mode'];
        }
        next:
        $this->_html = $this->_renderPassportYandex();
        return $this;
    }


    protected function _renderPassportYandex()
    {
        if ($this->_value)
        {
            $time = strtotime($this->_value);
            $day  = date('d', $time);
            $mo   = date('n', $time);
            $year = date('Y', $time);
        }
        else
        {
            $day  = '';
            $mo   = '';
            $year = '';

        }
        ob_start();
        ?>
        <input type="text" name="day" style="width: 2em" value="<?= $day ?>" maxlength="2" tabindex="12" placeholder="дд">
        <select name="month" tabindex="13"><option value="">месяц</option>
     <? for($month = 1; $month < 13; $month++)
        {
            ?><option<?
            if ($month == $mo)
            {
             ?>  selected="selected"<?
            }
?> value="<?= $month ?>"><?= $this->getMonthName($month) ?></option><?
        }
        ?>
        </select>
        <input type="text" style="width: 4em" name="year" value="<?= $year ?>" maxlength="4" tabindex="14" placeholder="гггг">
<?
        return ob_get_clean();
    }


    public function getMonthName($number = 0, $format = 1)
    {

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

}