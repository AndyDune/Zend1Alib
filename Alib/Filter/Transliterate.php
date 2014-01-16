<?php

namespace Alib\Filter;
use Alib\Form;
class Transliterate implements \Zend_Filter_Interface
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
//        echo $value;
        
        $value = trim($value);
        
        if (!$this->_builder)
            throw new Exception('Не указан обязательный параметр типа Form_Builder', 1);
        
        $field = $this->_formField;
        
        $data  = $this->_builder->getFormValues();
        

        $trans = new \Alib\String\Translit();
        if ($value)
        {
            $value = $trans->make($value);
        }    
        else if ($field and isset($data[$field]))
        {
            $value = $trans->make($data[$field]);
        }
        return $value;
    }
}