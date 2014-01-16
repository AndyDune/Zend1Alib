<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 05.12.12
 * Time: 9:27
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\String\Format\Email as EmailFormat;
class Email extends Record\DataType
{
    //Электроадрес указан не корректно!

    const WRONG_EMAIL = 'WRONG_EMAIL';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [
        self::WRONG_EMAIL    => "Электроадрес указан не корректно!"
        ];

    public function _processReady()
    {
        $result = true;

        $format = new EmailFormat($this->_value);
        if (!$format->check())
        {
            $this->addMessage(self::WRONG_EMAIL);
            $result = false;

        }
        return $result;
    }

}
