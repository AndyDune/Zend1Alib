<?php


namespace Alib\Form\Element;
class Login extends AbstractClass\Text
{
    
    public function init()
    {
        $this->addValidator('Regex', true, array("/^[^><'\"@]*$/iu", 
                            'messages' => array(\Zend_Validate_Regex::NOT_MATCH => 'Логин не должен содержать символы: @, <, >, \', "')));
        // Есть данные для проверки уникальности
        if ($table = $this->_builder->getTableName())
        {
            
            $val = new \Alib\Validate\StringOneAlphabet();
            $this->addValidator($val);
            
            $val = new \Alib\Validate\Db\NoRecordExistsRus(array('table' => $table, 'field' => $this->_builder->getTableField()));
            $val->setCheckLower();
            $this->addValidator($val);
        }
    }    
    
    
}

