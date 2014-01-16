<?php

namespace Alib\Form\Element;
class StringTransliterate extends AbstractClass\Text
{
    protected function _init()
    {
        $filter = new \Alib\Filter\Transliterate();
        $filter->setBuilder($this->_builder);
        $filter->setFormField($this->_builder->getFormField());
        $this->addFilter($filter);
        
//        $this->addFilter('Int');
        
        // Есть данные для проверки уникальности
        if ($table = $this->_builder->getTableName())
        {
            $val = new \Alib\Validate\Db\NoRecordExistsRus(array('table' => $table, 'field' => $this->_builder->getTableField()));
            $val->setCheckLower();
            if ($exclude = $this->_builder->getExclude())
                $val->setExclude($exclude);
            $this->addValidator($val);
        }
        
//        print_r($this->getFilters());
    }    
}