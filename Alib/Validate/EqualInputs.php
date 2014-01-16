<?php

/**
 * Проверка на наличие символов более чем одного алфавита.
 * Руские и английские буквы в строке присутствуют одновременно.
 * 
 * 
 */
namespace Alib\Validate;
class EqualInputs extends \Zend_Validate_Abstract
{
    /**
     *
     * @var Form_Builder
     */
    protected $_builder      = null;
    protected $_keyToCompare = null;
    protected $_method = 'post';


    const NOT_EQUAL = 'notEqual';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::NOT_EQUAL   => 'Значение поля не совпадает со сравниваемым',
//        self::INVALID => "'%value%' More then one alphabet",
    );

    
    /**
     * Sets default option values for this instance
     *
     * @param  boolean|Zend_Config $allowWhiteSpace
     * @return void
     */
    public function __construct($config = false)
    {
        if ($config instanceof \Zend_Config) 
        {
            $config = $config->toArray();
        }

        if (is_array($config)) 
        {
            if (isset($config['builder']))
            {
                $this->_builder = $config['builder'];
            }
            else
            {
                throw new Exception('Не передан обязательный параметр: builder', 500);
            }
            
            if (isset($config['key']))
            {
                $this->_keyToCompare = $config['key'];
            }
            else
            {
                throw new Exception('Не передан обязательный параметр: key', 500);
            }
            if (isset($config['method']) and in_array($config['method'], array('post', 'get')))
            {
                $this->_method = $config['method'];
            }            
            
        }
    }    
    
    /**
     * Defined by Zend_Validate_Interface
     *
     *
     * @param  string $value
     * @return boolean
     */
    public function isValid($value)
    {
        if ($this->_method == 'post')
            $data = $_POST;
        else
            $data = $_GET;
        $data = $this->_builder->getFormValues();
        //print_r($data);
        if (!isset($data[$this->_keyToCompare]) or trim($data[$this->_keyToCompare]) != $value)
        {
            $this->_error(self::NOT_EQUAL);
            return false;
        }
        return true;
    }

}
