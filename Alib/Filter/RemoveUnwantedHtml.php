<?php
/**
 * V.01
 * 
 * Фильтр HTML.
 * 
 */

namespace Alib\Filter;
use Alib\Form;
class RemoveUnwantedHtml implements \Zend_Filter_Interface
{

    protected $_builder      = null;
    protected $_formField      = '';

    /**
     *
     * @return void
     */
    public function __construct($options = array())
    {
        
    }

    public function setBuilder(Form\Builder $builder)
    {
        $this->_builder = $builder;
        return $this;
    }

    public function setFormField($value)
    {
        $this->_formField = $value;
        return $this;
    }
    

    /**
     * 
     * @param  string $value
     * @return string
     */
    public function filter($value)
    {

        return $value;
    }
}