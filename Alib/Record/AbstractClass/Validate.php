<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.07.12
 * Time: 14:59
 *
 *
 *
 */
namespace Alib\Record\AbstractClass;
abstract class Validate implements \Zend_Validate_Interface
{

    use \Alib\System\Traits\BuildClassName;

    /**
     * @var Record
     */
    protected $_record      = null;

    protected $_recordData  = array();

    protected $_recordId    = null;


    /**
     * The value to be validated
     *
     * @var mixed
     */
    protected $_value;


    protected $_messages = array();

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array();

    public function __construct()
    {

    }


    /**
     * @param \Alib\Record\AbstractClass\Record $name_record
     * @return RecordAbstract
     */
    public function setRecord($name_record)
    {
        if ($name_record instanceof \Alib\Record\AbstractClass\Record)
        {
            $this->_recordData = $name_record->getData();
            $this->_recordId = $name_record->getId();
            $this->_record = $this->_getRecordObject($name_record->getCreationString());
            //$this->_record = clone $name_record;
        }
        else
        {
            $this->_record = $this->_getRecordObject($name_record);
        }

        return $this;
    }


    /**
     * Returns array of validation failure messages
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

    /**
     * @param  string $messageKey
     * @param  string $value      OPTIONAL
     * @return void
     */
    protected function _error($messageKey, $value = null)
    {
        if ($messageKey === null) {
            $keys = array_keys($this->_messageTemplates);
            $messageKey = current($keys);
        }
        if ($value === null) {
            $value = $this->_value;
        }
        $this->_messages[$messageKey] = $this->_createMessage($messageKey, $value);
    }


    protected function _createMessage($messageKey, $value)
    {
        if (!isset($this->_messageTemplates[$messageKey])) {
            return null;
        }

        $message = $this->_messageTemplates[$messageKey];

        return $message;
    }

    /**
     * Sets the value to be validated and clears the messages and errors arrays
     *
     * @param  mixed $value
     * @return void
     */
    protected function _setValue($value)
    {
        $this->_value    = $value;
        $this->_messages = array();
    }
}
