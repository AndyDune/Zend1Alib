<?php


namespace rzn\lib\www;
abstract class Form_Builder_Abstract_Filter
{
    
    protected $_builder = null;
    protected $_options = null;
    
    
    public function __construct($options = null)
    {
        if ($options and is_array($options))
        {
            $this->_options = $options;
        }
    }
    
    final public function setBuilder($object)
    {
        $this->_builder = $object;
        return $this;
    }
    
    public function filter($values)
    {

    }

    
}
