<?php
namespace Alib;
class Exception extends \Exception
{
//    protected $trace;

//    public function getTrace()
//    {
//        return $this->trace;
//    }
    public function __construct($string = '', $code = 0)
    {
        parent::__construct($string, $code);
    }
}

