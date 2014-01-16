<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 04.12.12
 * Time: 15:53
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record\DataType;
use Alib\Record;
use Alib\String;
class Login extends Record\DataType
{

    const DLINA_MIN = 'DLINA_MIN';
    const DLINA_MAX = 'DLINA_MAX';
    const BAD_SYMBOLS = 'BAD_SYMBOLS';
    const NO_NECESSARILY_SYMBOLS = 'NO_NECESSARILY_SYMBOLS';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::DLINA_MAX    => "Длина больше установленной минимальной",
        self::DLINA_MIN    => "Длина меньше установленной минимальной",
        self::BAD_SYMBOLS    => "Есть запрещенные смволы",
        self::NO_NECESSARILY_SYMBOLS    => "Нет обязательных символов",
    );

    /**
     * Минимальная длина строки.
     *
     * @var null|int
     */
    protected $_minLength  = null;

    /**
     * Максимальная длина строки.
     *
     * @var null|int
     */
    protected $_maxLength  = null;


    protected $_normalToField = null;

    protected $_allowSymbols = ' .\-,%$#+*()_:!';

    protected $_allowSymbolsRus = '';

    public function setMinLength($value)
    {
        $this->_minLength = $value;
        return $this;
    }

    public function setMaxLength($value)
    {
        $this->_maxLength = $value;
        return $this;
    }

    public function normalToField($field)
    {
        $this->_normalToField = $field;
        return $this;
    }

    public function _processReady()
    {
        $result = true;
        if ($this->_minLength and strlen($this->_value) < $this->_minLength)
        {
            $this->addMessage(self::DLINA_MIN);
            $result = false;
            goto end;
        }

        if ($this->_maxLength and strlen($this->_value) > $this->_maxLength)
        {
            $this->addMessage(self::DLINA_MAX);
            $result = false;
            goto end;
        }

        // Поиск запрещенных символов
        if (preg_match('|[^' . $this->_allowSymbols . $this->_allowSymbolsRus . 'a-z0-9]|iu', $this->_value))
        {
            $this->addMessage(self::BAD_SYMBOLS);
            $result = false;
            goto end;
        }

        // Обязательные символы
        if (!preg_match('|[!a-z' . $this->_allowSymbolsRus . ']|iu', $this->_value))
        {
            $this->addMessage(self::NO_NECESSARILY_SYMBOLS);
            $result = false;
        }


        end:
        return $result;
    }

    public function processNearbyFields($result)
    {
        if ($this->_normalToField and $this->_value)
        {
            $string = new String($this->_value);
            $result[$this->_normalToField] = $string->tolower();
        }
        return $result;
    }

}
