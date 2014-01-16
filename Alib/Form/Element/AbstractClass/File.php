<?php


namespace Alib\Form\Element\AbstractClass;
use Alib\Form;
class File extends \Zend_Form_Element_File
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
        if ($val = $this->_builder->getMaxFileSize())
        {
            $this->setMaxFileSize($val);
        }

        $this->_init();
    }
    
    protected function _init()
    {
        
    }    
}
