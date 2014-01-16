<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 21.09.12
 * Time: 10:38
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record\Validate;
class StringOneAlphabet extends \Alib\Record\AbstractClass\Validate
{

    const INVALID = 'notInvalid';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array
    (
        self::INVALID => "Содержит символы более чем одного алфавита",
//        self::INVALID => "'%value%' More then one alphabet",
    );

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value contains only symbols of one alphabet
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $have_eng = preg_match('|[a-zA-Z]|iu', $value);
        $have_rus = preg_match('|[а-яА-Я]|iu', $value);
        $this->_setValue($value);
        if ($have_eng and $have_rus)
        {
            $this->_error(self::INVALID);
            return false;
        }

        return true;
    }

}
