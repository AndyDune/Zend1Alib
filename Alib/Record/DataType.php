<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 27.08.12
 * Time: 15:08
 *
 * Общий для ряда классов, описывающих типы данных для записи.
 *
 */

namespace Alib\Record;
abstract class DataType
{
    const REQ_FIELD      = 'REQ_FIELD';
    const EMPTY_FIELD    = 'EMPTY_FIELD';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplatesCommon = array
    (
        self::REQ_FIELD    => "Обязательное поле",
        self::EMPTY_FIELD  => "Значение не должно быть пустым",
    );

    /**
     * Имя поля.
     *
     * @var
     */
    protected $_field;

    /**
     * Флаг обязательности поля
     *
     * @var bool
     */
    protected $_require = false;

    /**
     * Флаг непустого значения
     *
     * @var bool
     */
    protected $_notEmpty = false;


    /**
     * Значение по умолчанию.
     *
     * @var bool
     */
    protected $_default = null;

    /**
     * Флаг готовности данных для записи.
     *
     * @var bool
     */
    protected $_ready = false;

    /**
     * @var Data
     */
    protected $_dataObject = null;

    protected $_value = null;
    protected $_valueSet = false;

    /**
     * @var AbstractClass\Record
     */
    protected $_record = null;

    /**
     * Флаг операции trim для данного.
     *
     * @var bool
     */
    protected $_trim = false;


    protected $_checkGetOnCreate = false;


    /**
     * Парметры, которые устанавливаются и извлекаются через метод __call()
     * Имена пармаметров - это имя вызванного метода без set(get)
     *
     * @var array
     */
    protected $_dataTypeParams = [];

    /**
     * На будущее.
     *
     * @var array
     */
    protected $_dataTypeParamsReq = [];

    public function __construct($field = null)
    {
        $this->_field = $field;
        $this->init();
    }

    /**
     * Возвращает индикатор, что тип данных указан явно.
     *
     * @return bool
     */
    public function is()
    {
        return true;
    }


    public function init()
    {
        return $this;
    }


    protected function _initSetRecord()
    {
        return $this;
    }

    public function checkGetOnCreate($flag = true)
    {
        $this->_checkGetOnCreate = $flag;
        return $this;
    }

    final public function setReqValues($name)
    {
        if (!is_array($name))
            $this->_dataTypeParamsReq = [$name];
        else
            $this->_dataTypeParamsReq = $name;

        return $this;
    }


    final public function setRecord(AbstractClass\Record $record)
    {
        $this->_record = $record;
        $this->_initSetRecord();
        return $this;
    }


    public function addMessage($code)
    {
        $this->_dataObject->addMessageWithField($this->_field, $this->_messageTemplates[$code]);
        return $this;
    }

    public function setRequire($flag = true)
    {
        $this->_require = $flag;
        return $this;
    }

    public function setDefault($value = null)
    {
        $this->_default = $value;
        return $this;
    }

    public function setNotEmpty()
    {
        $this->_notEmpty = true;
        return $this;
    }

    /**
     * Включение-отключение trim для данного.
     * По умолчанию процесс происходит.
     *
     * @param bool $value
     * @return DataType
     */
    public function trim($value = true)
    {
        $this->_trim = $value;
        return $this;
    }


    /**
     * Установка дынных.
     * Если данных нет в общем массиве - не устанавливать.
     *
     * @param $value
     * @return DataType
     */
    public function setValue($value)
    {
        $this->_valueSet = true;
        $this->_value = $value;
        if ($this->_trim)
            $this->_value = trim($this->_value);
        return $this;
    }

    /**
     * Возврат значения.
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }


    /**
     * Выбрать отформатированное значение согласно конкретному типу.
     * В дочерных классах перегружается.
     * Здесь же алиас для getValue()
     *
     * @return mixed
     */
    public function getFormatedValue()
    {
        return $this->getValue();
    }


    /**
     * Выбрать значение, подготовленное для вывода в поле формы.
     *
     * @return mixed
     */
    final public function getValuePreparedForForm()
    {
        if ($this->_checkGetOnCreate and !$this->_record->getId() and !$this->_value and isset($_GET[$this->_field]))
        {
            $this->_value = $_GET[$this->_field];
        }
        $this->_processValuePreparedForForm();
        return $this->_value;
    }


    protected function _processValuePreparedForForm()
    {
        return $this->_value;
    }


    /**
     * Запуск тестирования данных на соответсвие формату.
     *
     * @param Data $data для сохранения сообщений и выборки связных данных.
     * @return bool
     */
    public function ready(Data $data)
    {
        $this->_dataObject = $data;
        if ($this->_processReadyLocal())
            $this->_ready = $this->_processReady();
        return $this->_ready;
    }

    /**
     * Перегшружаемый метод для проверки и форматирования даннывх.
     *
     */
    protected function _processReadyLocal()
    {
        if ($this->_require and !$this->_valueSet)
        {
            $this->_dataObject->addMessageWithField($this->_field, $this->_messageTemplatesCommon[self::REQ_FIELD]);
            goto before_return;
        }

        if ($this->_notEmpty and
            $this->_value === ''
           )
        {
            $this->_dataObject->addMessageWithField($this->_field, $this->_messageTemplatesCommon[self::EMPTY_FIELD]);
            goto before_return;
        }


        /**
         * Если не уставновлены данные - устанавливаем по умолчанию.
         */
        if (!$this->_valueSet)
            $this->_value = $this->_default;

        $this->_ready = true;

        before_return:
        return $this->_ready;
    }

    /**
     * Запус процедур после успешной валидации всех данных.
     *
     * @return bool true - если надо подкорректироавать данные
     */
    public function processAfterValidateSuccess()
    {
        return false;
    }

    /**
     * Добавить в массив соседние(отличные от текущего) поля и вернуть измененный.
     *
     * @param $data
     * @return mixed
     */
    public function processNearbyFields($data)
    {
        return $data;
    }

    /**
     * Запус процедур после сохранения данных записи.
     *
     * @return bool true - если надо подкорректироавать данные
     */
    public function processAfterSave()
    {
        return false;
    }


    /**
     * Перегшружаемый метод для проверки и форматирования даннывх.
     *
     */
    protected function _processReady()
    {
        return true;
    }


    public function __call($name, $params = null)
    {

        $getOrSet = substr($name, 0, 3);
        switch($getOrSet)
        {
            case 'set':
                $paramName = substr($name, 3);
                $this->_dataTypeParams[$paramName] = $params[0];
                return $this;
            break;
            case 'get':
                $paramName = substr($name, 3);

                if (isset($this->_dataTypeParams[$paramName]))
                {
                    return $this->_dataTypeParams[$paramName];
                }
                if (in_array($paramName, $this->_dataTypeParamsReq))
                    throw new \Alib\Exception('Извлечение несуществующего но обязательного параметра ', 1);
                return null;
            break;

        }
        throw new \Alib\Exception('Запуск несуществующего метода', 1);
    }


    public function __toString()
    {
        return (string)$this->getFormatedValue();
    }


}
