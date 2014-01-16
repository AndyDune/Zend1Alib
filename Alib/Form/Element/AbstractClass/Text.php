<?php


namespace Alib\Form\Element\AbstractClass;
use Alib\Form;
class Text extends \Zend_Form_Element_Text
{
    /**
     *
     * @var Form\Builder
     */
    protected $_builder = null;

    
    /**
     * Необходимо для ссохранения состояний
     *
     * @return array
     */
    public function setMessages($array = null)
    {
        $this->_messages = $array;
        return $this;
    }    
    
    /**
     * Constructor
     *
     * $spec may be:
     * - string: name of element
     * - array: options with which to configure element
     * - Zend_Config: Zend_Config with options for configuring element
     *
     * @param  string|array|Zend_Config $spec
     * @param  array|Zend_Config $options
     * @return void
     * @throws Zend_Form_Exception if no element name after initialization
     */
    public function __construct($spec, $options = null, Form\Builder $builder = null)
    {
        $this->_builder = $builder;
        parent::__construct($spec, $options);
        $this->_initCommon();
    }    
    
    protected function _initCommon()
    {
        
        if ($val = $this->_builder->getDefaultValue())
        {
            $this->setValue($val);
        }
        
        if ($this->_builder->getStripTags())
        {
            $this->addFilter('StripTags');
        }
        
        $this->addFilter('StringTrim');
        
        if ($val = $this->_builder->getNotEmpty())
        {
            $this->addValidator('NotEmpty', true, $val);
        }
        
        if ($val = $this->_builder->getInArray())
        {
            $validator = new \Zend_Validate_InArray($val);
            $this->addValidator($validator, true);
        }
        
        
        if ($val = $this->_builder->getEqualInputs())
        {
            $val = new \Alib\Validate\EqualInputs(array('key' => $val, 'builder' => $this->_builder));
            $this->addValidator($val, true);
        }

        if ($val = $this->_builder->getStringLength())
        {
            $this->addValidator('StringLength', true, array($val['min'], $val['max']));
        }
        
        // Есть данные для проверки уникальности
        if ($table = $this->_builder->getTableName() and 0)
        {
            if ($field = $this->_builder->getTableField())
            {
                $val = new \Zend_Validate_Db_NoRecordExists(array('table' => $table, 'field' => $field));
                $this->addValidator($val);
            }
        }      
        $this->_init();
    }
    
    protected function _init()
    {
        
    }
}
