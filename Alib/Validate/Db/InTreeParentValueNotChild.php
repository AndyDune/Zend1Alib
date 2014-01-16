<?php

/**
 * Есть ли указанный id 
 * 
 */
namespace Alib\Validate\Db;
class InTreeParentValueNotChild extends \Zend_Validate_Db_Abstract
{
   const INVALID_PARENT = 'parentIsChild';
   const INVALID_PARENT_SELF = 'parentIsSelf';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_PARENT => "'%value%' является идентификатором одного из дочерних ветвей.",
        self::INVALID_PARENT_SELF => "'%value%' является собственным идентификатором записи.",
    );

    
    protected $_currentId = null;


    public function __construct($options)
    {
        if (isset($options['current_id']))
        {
           $this->_currentId = $options['current_id'];
        }
        parent::__construct($options);
    }
    
    public function isValid($value)
    {
        $valid = true;
        $this->_setValue($value);

        $value_temp = $value;
        
        if (!$this->_currentId)
            throw new Exception('Не указан обязательный ключ current_id');
        
        if ($value == $this->_currentId)
        {
                $this->_error(self::INVALID_PARENT_SELF);
                return false;
        }
        
        $check = true;
        do
        {
            $result = $this->_query($value_temp);

            if (!$result or !$result['parent_id']) 
            {
                $check = false;
            }
            else if ($result['parent_id'] == $this->_currentId)
            {
                $check = false;
                $valid = false;
                $this->_error(self::INVALID_PARENT);
            }
            $value_temp = $result['parent_id'];
            if (!$value_temp)
            {
                $check = false;
            }
            

        } while ($check);

        return $valid;
    }
    
    
    /**
     * Gets the select object to be used by the validator.
     * If no select object was supplied to the constructor,
     * then it will auto-generate one from the given table,
     * schema, field, and adapter options.
     *
     * @return Zend_Db_Select The Select object which will be used
     */
    public function getSelect()
    {
        if (null === $this->_select) {
            $db = $this->getAdapter();
            /**
             * Build select object
             */
            $select = new \Zend_Db_Select($db);
            //$select->from($this->_table, array($this->_field), $this->_schema);
            $select->from($this->_table, '*', $this->_schema);
            if ($db->supportsParameters('named')) {
                $select->where($db->quoteIdentifier($this->_field, true).' = :value'); // named
            } else {
                $select->where($db->quoteIdentifier($this->_field, true).' = ?'); // positional
            }
            if ($this->_exclude !== null) {
                if (is_array($this->_exclude)) {
                    $select->where(
                          $db->quoteIdentifier($this->_exclude['field'], true) .
                            ' != ?', $this->_exclude['value']
                    );
                } else {
                    $select->where($this->_exclude);
                }
            }
            $select->limit(1);
            $this->_select = $select;
        }
        
        return $this->_select;
    }    
    
}

