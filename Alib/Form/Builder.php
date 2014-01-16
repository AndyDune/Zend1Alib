<?php
/**
 * V.04
 * История:
 *  2011-09-12 Удалены устаревшие методы setStripTags и getStripTags.
 *  2011-08-29 Внедрение механизма фильтра массива результатов в комплексе.
 *  2011-08-01 В метод __call добавлена обработка произвольных эксессоров.
 *  2011-07-27 Новые методы setDefaultValue() и getDefaultValue()
 */


namespace Alib\Form;
use Alib;
class Builder
{
    protected $_form = null;
    protected $_required = false;

    
    protected $_tableName = null;
    protected $_tableGroup = null;
    protected $_tableRealization = null;

    // Для работы валидаторов с базой данных
    protected $_field = null;
    protected $_fieldToLower = true;


    protected $_stringLengthMin = null;
    protected $_stringLengthMax = null;
    
    protected $_equalInputs = null;
    
    protected $_notEmpty = false;
    
    protected $_inArray = null;
    
    
    protected $_exclude = null;
    
    protected $_defaultValue = null;
    
    
    protected $_optionsForNext = array();
    
    
    protected $_valuesFilter = array();
    
    

    public function __construct(\Zend_Form $form = null)
    {
        if (is_null($form))
        {
            $this->_form = new \Zend_Form();
        }
        else
        {
            $this->_form = $form;
        }
        $dir = \Zend_Registry::get('dir');
        $translator = new \Zend_Translate('array', $dir . '/modules/Www/configs/errors.php');
        $this->_form->setTranslator($translator);
    }
    
    
    
    public function addValuesFilter($name, $options = null)
    {
        if ($options)
        {
            if (is_string($options))
            {
                $options = array($options);
            }
        }
        $name = ucfirst($name);
        $class_name = 'Alib\\Form\\Builder\\Filter\\' . $name;
        $this->_valuesFilter[$name] = new $class_name($options);
        $this->_valuesFilter[$name]->setBuilder($this);
        return $this;
    }
    
    /**
     * Вернуть массив значений.
     * Если есть фильтры - внедрить их.
     * 
     */
    public function getValues()
    {
        $result = array();
        $values = $this->_form->getValues();
        if (!count($this->_valuesFilter))
            return $values;
        foreach($this->_valuesFilter as $filter)
        {
            $values = $filter->filter($values);
        }
        return $values;
    }    

    
    public function setInArray($array)
    {
        $this->_inArray = $array;
        return $this;
    }

    public function getInArray()
    {
        return $this->_inArray;
    }

    public function storeState()
    {
        $sess = Alib\Session::getNamespace('form');
        $sess->data = $this->_form->getValues();

//        print_r($sess->data);
//        die();


        $data = array();
        $err = array();
        $err_mess = array();
        foreach($this->_form as $key => $element)
        {
            $data[$key]     = $element->getValue();
            $err[$key]      = $element->getErrors();
            $err_mess[$key] = $element->getMessages();
        }
        $sess->errors = $err;
        $sess->errorMessages = $err_mess;
        return $this;
    }
    
    public function restoreState()
    {
        $sess = Alib\Session::getNamespace('form');
        if (!$sess->data or !is_array($sess->data))
            return false;
//        print_r($sess->data);
//        die();
        
        $this->_form->populate($sess->data);      
        
        $err = $sess->errors;
        $err_mess = $sess->errorMessages;

        
        foreach($this->_form as $key => $element)
        {
            if (isset($err[$key]))
            {
                $element->setErrors($err[$key]);
            }
            
            if (isset($err_mess[$key]))
            {
                $element->setMessages($err_mess[$key]);
            }
        }
        
//        $this->_form->isValid($sess->data);
        
        
/*        
        if ($sess->errors and is_array($sess->errors) and count($sess->errors))
              $this->_form->setErrors($sess->errors);
        if ($sess->errorMessages and is_array($sess->errorMessages) and count($sess->errorMessages))
            $this->_form->setErrorMessages($sess->errorMessages);
*/        
        $sess->unsetAll();
        
        return true;
    }

    public function loadData($data)
    {
        $this->_form->populate($data);
        return true;
    }
    
    
    
    /**
     *
boolean: Returns FALSE when the boolean value is FALSE. 

integer: Returns FALSE when an integer 0 value is given. Per default this validation is not activated and returns TRUE on any integer values. 

float: Returns FALSE when an float 0.0 value is given. Per default this validation is not activated and returns TRUE on any float values. 

string: Returns FALSE when an empty string '' is given. 

zero: Returns FALSE when the single character zero ('0') is given. 

empty_array: Returns FALSE when an empty array is given. 

null: Returns FALSE when an NULL value is given. 

php: Returns FALSE on the same reasons where PHP method empty() would return TRUE. 

space: Returns FALSE when an string is given which contains only whitespaces. 

object: Returns TRUE. FALSE will be returned when object is not allowed but an object is given. 

object_string: Returns FALSE when an object is given and it's __toString() method returns an empty string. 

object_count: Returns FALSE when an object is given, it has an Countable interface and it's count is 0. 

all: Returns FALSE on all above types.
     * 
     * @param type $value
     * @return Form_Builder
     */
    public function setNotEmpty($value = 'all')
    {
        $this->_notEmpty = $value;
        return $this;
    }
    
    public function getNotEmpty()
    {
        return $this->_notEmpty;
    }
    
    
    
    public function setExclude($value)
    {
        $this->_exclude = $value;
        return $this;
    }
    
    public function getExclude()
    {
        return $this->_exclude;
    }
    
    
    public function setTable($name, $group, $realization = 'base')
    {
        $this->_tableName = $name;
        $this->_tableGroup = $group;
        $this->_tableRealization = $realization;
        return $this;
    }
    
    public function getTableName()
    {
        if (!$this->_tableName)
            return false;
        
        $table = Alib\Db\Factory::table($this->_tableName, $this->_tableGroup, $this->_tableRealization);
        return $table->getTableName();
    }

/*    
    public function getTableField()
    {
        return $this->_field;
    }    
    public function setTableField($field)
    {
        $this->_field = $field;
        return $this;
    }
*/
    
    
    public function setRequired($value = true)
    {
        $this->_required = $value;
        return $this;
    }
    
    
    public function setEqualInputs($field)
    {
        $this->_equalInputs = $field;
        return $this;
    }

    public function getEqualInputs()
    {
        return $this->_equalInputs;
        return $this;
    }

/*    
    public function setDefaultValue($value)
    {
        $this->_defaultValue = $value;
        return $this;
    }

    public function getDefaultValue()
    {
        return $this->_defaultValue;
    }
*/    
    
    
    public function setStringLength($min, $max)
    {
        $this->_stringLengthMin = $min;
        $this->_stringLengthMax = $max;
        return $this;
    }

    public function getStringLength()
    {
        if ($this->_stringLengthMin === null)
            return null;
        $array = array(
        'min' => $this->_stringLengthMin,
        'max' => $this->_stringLengthMax
                     );
        return $array;
    }

    public function setStripTags_old($value = true)
    {
        $this->_stripTags = $value;
        return $this;
    }
    
    public function getStripTags_old()
    {
        return $this->_stripTags;
    }
    
    
    
    public function setLabel($value = '')
    {
        $this->_label = $value;
        return $this;
    }
    
    /**
     *
     * @param string $name имя поля
     * @param boolean $required обязательность
     * @return Form_Builder
     */
    public function addSpecialElementEmail($name, $required = null)
    {
        if ($required !== null)
            $this->_required = $required;
        $element = new Element\Email($name, null, $this);
        
        $element->setRequired($this->_required);
        if ($this->_label)
            $element->setLabel($this->_label);
        $this->_form->addElement($element);
        return $this->_resetForSpecial();
    }

    
    public function __call($name, $arguments) 
    {
        if (substr($name, 0, 17) == 'addSpecialElement')
        {
            if (!isset($arguments[1]))
                $arguments[1] = array();
            return $this->_addSpecialElement(substr($name, 17), $arguments[0], $arguments[1]);
        }
        
        if (substr($name, 0, 3) == 'set')
        {
            if (!isset($arguments[0]))
                $arguments[0] = null;
            
            $key = substr($name, 3);
            $this->_optionsForNext[$key] = $arguments[0];
            return $this;
        }
        
        if (substr($name, 0, 3) == 'get')
        {
            $key = substr($name, 3);
            if (isset($this->_optionsForNext[$key]))
                    return $this->_optionsForNext[$key];
            return null;
        }
        
        throw new Exception('Вызван несуществуюший метод');
    }


    
    /**
     *
     * @param string $name имя поля
     * @param boolean $required обязательность
     * @return Form_Builder
     */
    protected function _addSpecialElement($class, $name, $parameters = null)
    {
        $class_name = 'Alib\\Form\\Element\\' . $class;
        $element = new $class_name($name, $parameters, $this);
        if ($this->_required)
            $element->setRequired($this->_required);
        
        if ($this->_label)
            $element->setLabel($this->_label);
        
        $this->_form->addElement($element);
        return $this->_resetForSpecial();
    }
    
    
    /**
     *
     * @param string $name надпись на кнопке
     * @return Builder
     */    
    public function addElementSubmit($name = 'Сохранить')
    {
        $class_name = 'Alib\\Form\\Element\\Submit';
        $element = new $class_name($name);
        $this->_form->addElement($element);
        
        //$this->_form->addElement('submit', $name);
        return $this->_resetForSpecial();
    }
    
    
    /**
     *
     * @return Form_Builder 
     */
    protected function _resetForSpecial()
    {
        $this->_notEmpty     = false;
        $this->_inArray      = null;
        $this->_equalInputs  = null;
        $this->_tableName    = null;
        $this->_tableGroup   = null;
        $this->_required     = false;
        $this->_label        = '';
        $this->_field        = null;
        $this->_fieldToLower = false;
        $this->_exclude      = null;
        $this->_stringLengthMin = null;
        $this->_stringLengthMax = null;
        $this->_defaultValue    = null;
        
        // Произвольные параметры
        $this->_optionsForNext  = array();
        
        return $this;
    }
    
    /**
     *
     * @return Zend_Form
     */
    public function get()
    {
//        print_r($this->_optionsForNext);
        //$this->_form->addElementFilters(array('StringTrim'));
        return $this->_form;
    }

    
    /**
     *
     * @return Zend_Form
     */
    public function getFormValues()
    {
        return $this->_form->getUnfilteredValues();
    }
    
    
}