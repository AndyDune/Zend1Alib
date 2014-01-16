<?php
/**
 * V.02
 * 
 * Версии
 *  2011-08-26 Устранена ошибка при пустом массиве.
 * 
 */

namespace Alib\Form\Element;
class SelectFromTree extends AbstractClass\Select
{
    
    /**
     * Use formSelect view helper by default
     * @var string
     */
    public $helper = 'formSelectTree';
    
    
    protected $_subtreeName = 'subtree';
    protected $_keyValue = 'id';
    protected $_keyTitle = 'title';
    protected $_default = null;
    protected $_treeLevel = 1;

    protected $_classPrefix = 'level-';
    

    public function init()
    {
        if ($table = $this->_builder->getTableName())
        {
            if (!$current_id = $this->_builder->getCurrentId())
                throw new Exception('Не указан обязательный ключ current_id');
            $val = new \Alib\Validate\Db\InTreeParentValueNotChild(array('table' => $table,
                        'field'      => 'id',
                        'current_id' => $current_id // Текущий идентификатор
                ));
            $this->addValidator($val);
        }        
        
    } 
    
    
    public function setSubtree($value)
    {
        $this->_subtreeName = $value;
        return $this;
        
    } 
    
    /**
     * Add an option
     *
     * @param  string $option
     * @param  string $value
     * @return Zend_Form_Element_Multi
     */
    public function addMultiOption($option, $value = '', $attr = null)
    {
        $option  = (string) $option;
        $this->_getMultiOptions();
        if (!$this->_translateOption($option, $value)) 
        {
            if (!$attr)
                $this->options[$option] = $value;
            else
            {
                $this->options[$option] = array();
                $this->options[$option]['value'] = $value;
                $this->options[$option]['params'] = $attr;
            }
        }
        return $this;
    }    
    
    public function setDefault($value)
    {
        if (is_array($value) and isset($value['value']) and isset($value['title']))
            $this->_default = $value;
        return $this;
        
    } 
    
    public function setData($value)
    {
        if ($this->_default)
        {
            $this->addMultiOption($this->_default['value'], $this->_default['title'], array('class' => $this->_classPrefix . 1));
        }
        if (!$value or !count($value))
            return $this;
        foreach($value as $value)
        {
            $this->addMultiOption($value[$this->_keyValue], $value[$this->_keyTitle], array('class' => $this->_classPrefix . 1));
            if (isset($value[$this->_subtreeName]) and $value[$this->_subtreeName] and count($value[$this->_subtreeName]))
            {
                $this->_setData($value[$this->_subtreeName], 1);
            }
        }
        return $this;
    } 

    public function _setData($value, $level)
    {
        $level = $level + 1;
        foreach($value as $value)
        {
            $this->addMultiOption($value[$this->_keyValue], $value[$this->_keyTitle], array('class' => $this->_classPrefix . $level));
            if (isset($value[$this->_subtreeName]) and $value[$this->_subtreeName] and count($value[$this->_subtreeName]))
            {
                $this->_setData($value[$this->_subtreeName], $level);
            }
        }
        
        return $this;
    } 
    
    
    /**
     * Is the value provided valid?
     *
     * Autoregisters InArray validator if necessary.
     *
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        if ($this->registerInArrayValidator()) {
            if (!$this->getValidator('InArray')) {
                $multiOptions = $this->getMultiOptions();
                $options      = array();

                foreach ($multiOptions as $opt_value => $opt_label) {
                    if (is_array($opt_label))
                        $opt_label = $opt_label['value'];
                    // optgroup instead of option label
                    if (is_array($opt_label)) {
                        $options = array_merge($options, array_keys($opt_label));
                    }
                    else {
                        $options[] = $opt_value;
                    }
                }

                $this->addValidator(
                    'InArray',
                    true,
                    array($options)
                );
            }
        }
        return parent::isValid($value, $context);
    }    
    
    
    
}