<?php
namespace Alib;
class Test
{
    static public function pr($value, $exit = false)
    {
        ob_start();
            echo '<pre>';
            print_r($value);
            echo '</pre>';
        $string = ob_get_clean();
        echo $string;
        if ($exit)
            die();
    }
}