<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 14.12.12
 * Time: 15:41
 *
 *
 *
 */
namespace Alib\View\Helper;
use Alib;
class ArrayExtractValuesWithPrefix extends \Alib\View\HelperAbstract
{
    protected $_data = [];

    public function direct($prefix = null, $array = [], $params = [])
    {
        if (!$prefix)
            goto end;

        $processed = [];

        if (isset($params['delete']) and $params['delete'])
            $delete = true;
        else
            $delete = false;

        foreach($array as $key => $value)
        {
            $pos = strrpos($key, $prefix);
            if ($pos === 0)
            {
                if ($delete)
                    $key = substr($key, strlen($prefix));
                $processed[$key] = $value;
            }
        }

        $this->_data = $processed;
        end:
        return $this;
    }

    public function get()
    {
        return $this->_data;
    }
}
