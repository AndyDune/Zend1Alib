<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 07.11.12
 * Time: 13:49
 *
 *
 */
namespace Alib\Record\Form;
use Alib\Record;
class DataCheck implements \ArrayAccess
{
    /**
     * @var \Alib\Record\Data
     */
    protected $_dataType;

    protected $_data = [];

    protected $_trim = false;

    /**
     * @var \Alib\Session\AbstractClass
     */
    protected $_session = null;


    /**
     * @var \Zend_Controller_Request_Http
     */
    protected $_request;

    protected $_ready = true;


    protected $_sessionKeyName  = null;
    protected $_sessionKeyValue = null;
    protected $_sessionKeyMessage = 'Потеря сессии. Повторите попытку.';

    public function __construct(Record\Data $dataType, $request, \Alib\Session\AbstractClass $session)
    {
        $this->_request = $request;
        $this->_session = $session;
        $this->_dataType = $dataType;

    }

    public function ready()
    {
        $ready = $this->_ready;
        if (!$ready)
            goto ready_end;
        if ($this->_sessionKeyName)
        {
            $param = $this->_request->getParam($this->_sessionKeyName);
            if (!$param or !$this->_session->{$this->_sessionKeyName} or $param != $this->_session->{$this->_sessionKeyName})
            {
                $this->_dataType->addMessage($this->_sessionKeyMessage);
                $ready = false;
                goto ready_end;
            }
        }
        ready_end:
        $this->_dataType->set($this->_data);
        return $ready;
    }


    public function useSessionKey($field, $params = [])
    {
        $this->_sessionKeyName = $field;
        if (isset($params['message']))
            $this->_sessionKeyMessage = $params['message'];
        return $this;
    }

    public function getSessionKeyParams()
    {
        if (!$this->_sessionKeyName)
            return null;
        $value = md5(uniqid());
        $this->_session->{$this->_sessionKeyName} = $value;
        return ['field' => $this->_sessionKeyName, 'value' => $value];
    }


    public function setSession(\Alib\Session\AbstractClass $session)
    {
        $this->_session = $session;
        return $this;
    }

    public function fieldEqualTo($field, $params = [])
    {
        $value = trim($this->_request->getParam($field));

        if (!isset($params['field']))
            throw new \Alib\Exception('Необходимый параметр для этого метода field пропущен', 1);
        if (strcmp($value, trim($this->_request->getParam($params['field']))) != 0)
        {
            $this->_ready = false;
            if (isset($params['message']))
                $this->_dataType->addMessage($params['message']);

        }
        return $this;

    }

    public function fieldNotEmpty($field, $params = [])
    {
        $param = trim($this->_request->getParam($field));
        if (isset($params['min']))
            $min = $params['min'];
        else
            $min = 0;
        $this->_data[$field] = $param;
        if (!$param or strlen($param) < $min)
        {
            $this->_ready = false;
            if (isset($params['message']))
                $this->_dataType->addMessage($params['message']);
        }
        return $this;
    }

    public function trim()
    {
        $this->_trim = true;
        return $this;
    }

    public function fieldDate($field, $collect = null)
    {
        if (!$collect)
        {
            $date = trim($this->_request->getParam($field));
            if ($date)
                $this->_data[$field] = $date; //todo сделать проверку корректности
            goto end;
        }

        if (!array_key_exists('year', $collect) or !array_key_exists('month', $collect)
            or !array_key_exists('month', $collect))
        {
            goto end;
            $this->_ready = false;
            $this->_dataType->addMessage('Неверный формат даты');
            goto end;
        }

        $year  = $this->_request->getParam($collect['year']);
        $month = $this->_request->getParam($collect['month']);
        $day   = $this->_request->getParam($collect['day']);

        if (!$day or !$month or !$year)
            goto end;

        $dateFormat = new \Alib\Format\DateAssembly([
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);

        $date = $dateFormat->getDateTime();
        if (!$date)
        {
            $this->_ready = false;
            $this->_dataType->addMessageWithField($field, 'Неверный формат даты');
            goto end;
        }

        $this->_data[$field] = $date;
        end:
        return $this;
    }

    /**
     * Метод оставляет (или дополняет) данные только с указанными полями.
     * Рекомендую применять этот метод непосредственно перед отдачей данных на сохранение, т.е. перед getData()
     *
     * @param $fields
     * @param null $default
     * @return DataCheck
     */
    public function fields($fields, $default = null)
    {
        if (!is_array($fields))
            $fields = [$fields];
        $result = [];
        foreach($fields as $value)
        {
            if (!array_key_exists($value, $this->_data))
                $result[$value] = $this->_request->getParam($value, $default);
            else
                $result[$value] = $this->_data[$value];
        }
        $this->_data = $result;
        $this->_fields = $fields;
        return $this;
    }

    public function getData()
    {
        return $this->_data;
    }


    public function __get($key)
    {
        return $this->offsetGet($key);
    }

//////////////////////////////////////////////////////////////////
///////////////////////////////     Методы интерфейса ArrayAccess
    public function offsetExists($key)
    {
        return isset($this->_data[$key]);
    }
    public function offsetGet($key)
    {
        if (isset($this->_data[$key]))
            return $this->_data[$key];
        else
            return null;
    }

    public function offsetSet($key, $value)
    {
        $this->_data[$key] = $value;
    }
    public function offsetUnset($key)
    {
        unset($this->_data[$key]);
    }


}
