<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 20.09.12
 * Time: 10:11
 *
 *
 */
namespace Alib\Record\Validate\Db;
use Alib\Record\Validate;
abstract class RecordAbstract extends \Alib\Record\AbstractClass\Validate
{
    use \Alib\System\Traits\BuildClassName;

    /**
     * @var \Alib\Record\AbstractClass\Record
     */
    protected $_record      = null;

    protected $_recordData  = array();

    protected $_recordId    = null;

    protected $_field    = null;

    protected $_exclude  = null;

    /**
     *
     * The following option keys are supported:
     * 'record'   => The database table to validate against
     * 'field'   => The field to check for a match
     * 'exclude' => An optional where clause or field/value pair to exclude from the query
     *
     * @param array
     */
    public function __construct($options)
    {
        if (func_num_args() > 1)
        {
            $options       = func_get_args();
            $temp['field'] = array_shift($options);
            $temp['field_type'] = array_shift($options);
            if (!empty($options)) {
                $temp['exclude'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('field_type', $options))
        {
            $this->_fieldType = $options['field_type'];
        }

        if (array_key_exists('exclude', $options))
        {
            $this->setExclude($options['exclude']);
        }

        if (array_key_exists('field', $options))
        {
            $this->setField($options['field']);
        }
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

    public function setField($name_field)
    {
        $this->_field = $name_field;
        return $this;
    }


    public function setExclude($value)
    {
        $this->_exclude = $value;
        return $this;
    }

    public function getMessages()
    {
        return $this->_messages;
    }
}
