<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 04.12.12
 * Time: 11:46
 *
 * Использование хелпероа из произвольного модуля без дополнительной настройки.
 * Дополнительная настройка, как то подключение еще одной папки хелперов может внести ужас,
 * если есть совпадающие имена в новом пространстве и старом.
 *
 *
 */
namespace Alib\View\Helper;
use Alib;
class Helper extends \Alib\View\HelperAbstract
{
    protected $_helpers = [];
    public function helper()
    {
        $params = func_get_args();
        $helperNameFull = array_shift($params);
        if (!count($params))
            $params = null;

        $parts = explode(':', $helperNameFull);
        $helperName = $parts[0];

        if (array_key_exists($helperNameFull, $this->_helpers))
        {
            $helper = $this->_helpers[$helperNameFull];
            goto end;
        }

        if (isset($parts[1]) and $parts[1])
            $module = ucfirst($parts[1]);
        else
            $module = Alib\Registry::get('module');
        $className = '\\Application\\' . $module . '\\View\\Helper\\' . ucfirst($helperName);
        $helper = new $className();
        $helper->setView($this->view);
        $this->_helpers[$helperNameFull] = $helper;
        end:
        return call_user_func_array([$helper, 'direct'], $params);
        return $helper;
    }
}