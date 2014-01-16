<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 27.08.12
 * Time: 14:34
 *
 * Строка.
 *
 */
namespace Alib\Record\DataType;
use Alib\Record;
class String extends Record\DataType
{

    const DLINA_MIN = 'DLINA_MIN';
    const DLINA_MAX = 'DLINA_MAX';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::DLINA_MAX    => "Длина строки больше установленной минимальной",
        self::DLINA_MIN    => "Длина строки меньше установленной минимальной",
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

    /**
     * Флан экранирования спецсимволов html при сохранении.
     *
     * @var bool
     */
    protected $_htmlEscape = false;

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

    public function setHtmlEscape($value = true)
    {
        $this->_htmlEscape = $value;
        return $this;
    }

    public function _processReady()
    {
        $result = true;
        if ($this->_minLength and strlen($this->_value) < $this->_minLength)
        {
            $this->addMessage(self::DLINA_MIN);
            $result = false;
        }
        return $result;
    }

}

