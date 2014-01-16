<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.05.12
 * Time: 14:37
 */


namespace Alib\Form\Element\AbstractClass;
use Alib\Form;
class Hidden extends \Zend_Form_Element_Hidden
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



        if ($val = $this->_builder->getStringLength())
        {
            $this->addValidator('StringLength', true, array($val['min'], $val['max']));
        }


        $this->_init();
    }

    protected function _init()
    {

    }
}

