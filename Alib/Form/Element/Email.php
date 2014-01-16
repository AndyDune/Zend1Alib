<?php


namespace Alib\Form\Element;
class Email extends AbstractClass\Text
{
    
    public function init()
    {
        $this->addValidator('EmailAddress')
             ->addFilter('StringToLower', array('encoding' => 'UTF-8'))
             ->addFilter('StringTrim');
        
        
        // Есть данные для проверки уникальности
        if ($table = $this->_builder->getTableName())
        {
//            echo $this->_builder->getTableField();die();
            $val = new \Zend_Validate_Db_NoRecordExists(array('table' => $table, 'field' => $this->_builder->getTableField()));
            $this->addValidator($val);
        }
        
        
    }    
}
