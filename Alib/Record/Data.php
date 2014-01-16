<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 25.07.12
 * Time: 11:27
 *
 * Объекты класса инкарпсулируют в себе данные записи для сохранения с формы.
 * Содержат первоначальные данные, промежуточные, сообщения об ошибках.
 */
namespace Alib\Record;
use Alib\Session;
//use Alib\Record\DataType;
class Data implements \ArrayAccess, \Iterator, \Countable
{
    protected $_dataOriginal = array();
    protected $_dataProcess = array();

    protected $_processHavingDataOnly = false;

    /**
     * Общие сообщения без привязки к полям.
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * Сообщения при обработке данных валидаторами.
     * <имя поля> => array(<сообщение>, <сообщение>, ...)
     *
     * @var array
     */
    protected $_messagesWithField = array();

    /**
     * ПОдробное описание сообщений
     * <имя поля> => array(<массив подробностей>, <массив подробностей>, ...)
     * <массив подробностей> => array(<класс, объект которого сгенерил сообщение>, <код сообщения внутри класса>)
     *
     * @var array
     */
    protected $_messagesDetails = array();


    /**
     * Данные для автоматического формирования формы редактирования записи в админке.
     *
     * Может быть вшито, либо динамически вставляться из конфигов.
     *
     * @var array
     */
    protected $_dataFormat = [];


    /**
     * @var AbstractClass\Record
     */
    protected $_recordObject;


    /**
     * @var \Zend_Session_Namespace
     */
    protected $_session;

    protected $_saved   = false;
    protected $_trySave = false;


    /**
     * Промежуточный массив объектов - типов данных.
     *
     * @var array
     */
    protected $_dataTypeObjects = [];

    /**
     * Флаг остановки валидации после встречи первой ошибки.
     *
     * @var bool
     */
    protected $_stopProcessMeetError = false;


    protected $_checkOnlyFields = [];

    public function __construct(AbstractClass\Record $recordObject)
    {
        $this->_recordObject = $recordObject;
        $this->_dataOriginal = $this->_dataProcess = $this->_recordObject->getData();
        //$this->init();
        $this->setDataFormat($this->_recordObject->getDataFormat());
        $this->_session = Session::getNamespace('record-save-data');
    }

    /**
     * Включение в проверку и сохранение только указанных полей.
     *
     * @param $fields
     * @return Data
     */
    public function checkOnlyFields($fields)
    {
        $this->_checkOnlyFields = $fields;
        return $this;

    }

    public function clean()
    {
        $this->_dataTypeObjects = [];
        $this->_saved   = false;
        $this->_trySave = false;
        $this->_messages = [];
        $this->_messagesWithField = [];
        return $this;
    }

    /**
     * Запрос записи - родителя.
     *
     * @return AbstractClass\Record
     */
    public function getRecordObject()
    {
        return $this->_recordObject;
    }

    /**
     * Проиниицилизировать объект данными из записи - родителя.
     *
     */
    public function init()
    {
        //echo $this->_recordObject->getTableName(), '-initDataObject<br>'; // todo убрать это
        $this->clean();
        $this->_dataOriginal = $this->_dataProcess = $this->_recordObject->getData();
        return $this;
    }

    /**
     * Извлеч сохраненные данные из сессии.
     *
     * @return Data
     */
    public function restoreResults($noStrong = false)
    {
        $data = $this->_session->data;
        if ($noStrong) // Не учитывается ссотояние связанной записи
            goto jump;
        if($this->_session->id and $this->_session->id != $this->_recordObject->getId()
           or
            ($this->_session->recordString != $this->_recordObject->getCreationString())
        )
        {
            $this->_session->unsetAll();
            return $this;
        }
        jump:
        //$this->_saved = $this->_session->saved;
        if ($data and is_array($data) and count($data))
        {
            /*
             * В данных с формы может не быть всех необходимых данных для рисования.
             */
            foreach($data as $key => $value)
            {
                $this->_dataProcess[$key] = $value;
            }
            //$this->_dataProcess = $data;
        }

        $data = $this->_session->messagesWithField;
        if ($data and is_array($data) and count($data))
        {
            $this->_messagesWithField = $data;
        }

        $data = $this->_session->messages;
        if ($data and is_array($data) and count($data))
        {
            $this->_messages = $data;
        }
        if ($this->_session->saved)
            $this->_saved = $this->_session->saved;

        if ($this->_session->try)
            $this->_trySave = $this->_session->try;

        if ($this->_saved)
            $this->_session->unsetAll();
        else
        {
            unset($this->_session->saved);
            unset($this->_session->try);
            unset($this->_session->messages);
            unset($this->_session->messagesWithField);
        }
        return $this;
    }

    /**
     * Сохранить данные в сессии для отображения в следующем запросе.
     *
     * @return Data
     */
    public function storeResults($success = false)
    {
        //$this->_dataProcess = $this->_recordObject->getData();
        $this->_session->id = $this->_recordObject->getId();
        $this->_session->recordString = $this->_recordObject->getCreationString();
        $this->_session->data = $this->_dataProcess;
        //\Alib\Test::pr($this->_session->data, 1);
        $this->_session->messagesWithField = $this->_messagesWithField;
        $this->_session->messages = $this->_messages;
        $this->_session->saved = $this->_saved;
        $this->_session->try = $this->_trySave;

        return $this;
    }

    public function getData()
    {
        return $this->_dataProcess;
    }


    public function set($key, $value = null)
    {
        if (is_array($key))
            $this->_dataProcess = $key;
        else
            $this->_dataProcess[$key] = $value;
        return $this;
    }

    public function get($key)
    {
        return $this->_dataProcess[$key];
    }


    /**
     * Установить сообщение для поля.
     *
     * @param $field имя поля
     * @param $value текст сообщения
     * @return Data
     */
    public function setMessageWithField($field, $value)
    {
        if (is_array($value))
            $this->_messagesWithField[$field] = $value;
        else
            $this->_messagesWithField[$field] = array($value);
        return $this;
    }

    /**
     * Добавить сообщение для поля.
     *
     * @param $field имя поля
     * @param $value текст сообщения
     * @return Data
     */
    public function addMessageWithField($field, $value)
    {
        if (!isset($this->_messagesWithField[$field]))
        {
            $this->_messagesWithField[$field] = [];
        }

        if (is_array($value))
        {
            foreach($value as $key => $val)
            {
                $this->_messagesWithField[$field][$key] = $val;
            }
        }
        else
        {
            $this->_messagesWithField[$field][] = $value;
        }

        return $this;
    }


    /**
     * Устновить общее сообщение.
     *
     * @param $value текст сообщения
     * @return Data
     */
    public function setMessage($value)
    {
        $this->_messages = array($value);
        return $this;
    }

    /**
     * Добавить общее сообщение.
     *
     * @param $value текст сообщения
     * @return Data
     */
    public function addMessage($value)
    {
        $this->_messages[] = $value;
        return $this;
    }


    /**
     * Перебор массива, описывающего данные для сохранения и вызов фильтров.
     *
     * @return array отфильтрованные данные
     */
    public function filter()
    {
        $result = $this->_dataProcess;
        if (!count($this->_dataFormat)) return $result;
        foreach($this->_dataFormat as $field => $array)
        {
            if (!array_key_exists($field, $this->_dataProcess))
                continue;
            if (!array_key_exists('filter', $array) or !is_array($array['filter']) or !count($array['filter']))
            {
                continue;
            }
            foreach ($array['filter'] as $filterParams)
            {
                $filterClassName = 'Alib\\Record\\Filter\\' . $filterParams[0];
                if (!array_key_exists(1, $filterParams))
                    $filterParams[1] = null;
                /** @var $validatorObject AbstractClass\Filter */
                $filter = new $filterClassName($filterParams[1]);
                $filter->setRecord($this->_recordObject);
                $result[$field] = $filter->filter($this->_dataProcess[$field]);

            }
        }
        return $result;
    }

    /**
     * Перебор массива, описывающего данные для сохранения и вызов валидаторов.
     *
     * @param $data
     * @return bool успех или провал валидации
     */
    public function validate($data)
    {
        $goodResult = true;
        if (!count($this->_dataFormat)) goto before_return;
        foreach($this->_dataFormat as $field => $array)
        {
            if (!array_key_exists($field, $this->_dataProcess))
                continue;
            if (!array_key_exists('validator', $array) or !is_array($array['validator']) or !count($array['validator']))
            {
                continue;
            }
            foreach ($array['validator'] as $validatorParams)
            {
                $validatorClassName = '\Alib\\Record\\Validate\\' . $validatorParams[0];
                if (!array_key_exists(1, $validatorParams))
                    $validatorParams[1] = null;
                /** @var $validatorObject AbstractClass\Validate */
                $validatorObject = new $validatorClassName($validatorParams[1]);
                $validatorObject->setRecord($this->_recordObject);
                if (!$validatorObject->isValid($this->_dataProcess[$field]))
                {
                    $this->addMessageWithField($field, $validatorObject->getMessages());
                    $goodResult = false;
                    if ($this->_stopProcessMeetError) // Остановка проверки после первой ошибки
                        goto before_return;
                }

            }
        }

        before_return:
        return $goodResult;
    }

    /**
     * Инициилизация формата данных для рисования формы и фильтрования и валидации данных.
     *
     * Устанавливается из записи - объект создается только в записи.
     *
     * @param $data
     * @return Data
     */
    public function setDataFormat($data)
    {
        $this->_dataFormat = $data;
        return $this;
    }

    /**
     * Возврат данных для рисования формы.
     *
     *
     * @return array
     */
    public function getDataFormat()
    {
        return $this->_dataFormat;
    }


    /**
     * Необходим для неполного сохранения данных записи.
     * Когда нужно сохранить только одно поле и запустить для него процесс через DataType.
     *
     * @param bool $flag
     * @return Data
     */
    public function processHavingDataOnly($flag = true)
    {
        $this->_processHavingDataOnly = $flag;
        return $this;
    }


    protected function _checkFields()
    {
        if (!count($this->_checkOnlyFields))
            return $this->_dataProcess;
        $data = [];
        foreach($this->_checkOnlyFields as $value)
        {
            if (array_key_exists($value, $this->_dataProcess))
                $data[$value] = $this->_dataProcess[$value];
        }
        $this->processHavingDataOnly(true);
        return $data;
    }

    /**
     * Запуск полной обработки данных.
     * Для общих валидаторов и фильтров обрабатываются тольк существующие записи в массиве.
     * Для DataType объектов вставляются умолчания и обрабатываются данные на обязательность.
     *
     * Для блокировани я этой плюшки измените флаг через метод processHavingDataOnly().
     *
     * @return bool
     */
    public function ready()
    {
        $this->_trySave = true;

        $this->_dataProcess = $this->_checkFields();

        //echo 'filter-dataObject<br>'; // todo стереть это
        $this->_dataProcess = $this->filter();

        //echo 'ready-dataObject-_processWithDataTypeClasses<br>'; // todo стереть это
        if (!$this->_processWithDataTypeClasses($this->_dataProcess))
        {
            return false;
        }

        //echo 'ready-dataObject-validate<br>'; // todo стереть это
        if (!$this->validate($this->_dataProcess))
        {
            return false;
        }
        //echo 'ready-dataObject-validateAfter<br>'; // todo стереть это

        //echo 'dataType-ready-_processWithDataTypeClassesAfterValidate<br>'; // todo стереть это
        $this->_processWithDataTypeClassesAfterValidate();

        $this->_saved = true;
        return true;
    }

    public function _processWithDataTypeClasses($data)
    {
        $goodResult = true;
        if (!count($this->_dataFormat)) goto before_return;
        foreach($this->_dataFormat as $field => $array)
        {
            $dataTypeObject = $this->getDataTypeObject($field, $array);
            if (!$dataTypeObject->is())
            {
                continue;
            }
            if ($this->_processHavingDataOnly and !array_key_exists($field, $this->_dataProcess))
                continue;
            if (!$dataTypeObject->ready($this))
            {
                $goodResult = false;
                if ($this->_stopProcessMeetError) // Остановка проверки после первой ошибки
                    goto before_return;
            }
            $this->_dataProcess[$field] = $dataTypeObject->getValue();

        }

        before_return:
        return $goodResult;
    }

    /**
     * Запуск после общей валидации, но до передачи данных объекте записи.
     * Можно корректировать данные для передачи или совершать действия до окончательного сохранения.
     *
     * Можно корректировать данные.
     *
     * @return Data
     */
    protected function _processWithDataTypeClassesAfterValidate()
    {
        foreach($this->_dataTypeObjects as $field => $object)
        {
            if ($this->_processHavingDataOnly and !array_key_exists($field, $this->_dataProcess))
                continue;

            if ($object->processAfterValidateSuccess())
                $this->_dataProcess[$field] = $object->getValue();
        }
        return $this;
    }


    /**
     * Запускается после сохранения основных данных записи.
     * Данные, которые будут возвращены методом будут записаны в таблицу записи.
     *
     *
     * @return array непустой массив повлечет повторную запись данных.
     */
    public function commit()
    {
        $correctedData = array();
        foreach($this->_dataTypeObjects as $field => $object)
        {
            if ($object->processAfterSave())
            {
                $correctedData[$field] = $object->getValue();
            }
            $correctedData = $object->processNearbyFields($correctedData);
        }
        return $correctedData;
    }



    /**
     *
     *
     * @param $field
     * @param $array
     * @return DataType
     */
    public function getDataTypeObject($field, $array = null)
    {
        if (isset($this->_dataTypeObjects[$field]))
            return $this->_dataTypeObjects[$field];
        if (!$array)
        {
            if (array_key_exists($field, $this->_dataFormat))
            {
                $array = $this->_dataFormat[$field];
            }
            else
                $array = null;
        }
        if ($array and !array_key_exists('class', $array))
        {
            $array = null;
        }

        if ($array)
            $className = 'Alib\\Record\\DataType\\' . $array['class'];  // Имя класса в пространстве
        else
            $className = 'Alib\\Record\\DataType\\Common';  // Имя класса в пространстве
        //if ($field == 'logo') die();
        /** @var $dataTypeObject DataType */
        $dataTypeObject = new $className($field);

        $this->_dataTypeObjects[$field] = $dataTypeObject;

        $dataTypeObject->setRecord($this->_recordObject);
        //if ($field == 'logo') die(); // test
        // Установка сохраняемого значения, если оно есть
        if (array_key_exists($field, $this->_dataProcess))
        {
            $dataTypeObject->setValue($this->_dataProcess[$field]);
        }
        else if ($field == $this->_recordObject->getIdFieldName())
        {
            $dataTypeObject->setValue($this->_recordObject->getId());
        }

        // Установка параметров
        if ($array and array_key_exists('params', $array) and is_array($array['params']))
        {
            foreach($array['params'] as $param)
            {
                if (is_array($param))
                {
                    if (count($param) == 1) // Только метод
                    {
                        $dataTypeObject->{$param[0]}();
                    }
                    else if (is_array($param[1])) // Несколько параметров
                    {
                        call_user_func_array(array($dataTypeObject, $param[0]), $param[1]);
                    }
                    else // Один параметр
                    {
                        call_user_func(array($dataTypeObject, $param[0]), $param[1]);
                        $dataTypeObject->{$param[0]}($param[1]); // Один параметр
                    }

                }
                else
                {
                    $dataTypeObject->{$param}();
                }
            }
        }
        return $dataTypeObject;
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }


    public function displayMessages($field = null, $params = null)
    {
        if (array_key_exists('all', $params))
            $all = true;
        else
            $all = false;
        if ($field)
            $messages = $this->getMessagesWithField($field);
        else
        {
            if (array_key_exists('all', $params))
                $messages = $this->getMessagesAll();
            else
                $messages = $this->getMessages();
        }
        if ($messages)
        {
            if (array_key_exists('div_class', $params))
            {
                ?><div class="<?= $params['div_class'] ?>"><?
            }

            ?><ul class="messages"><?
            foreach($messages as $message)
            {
                ?><li><?= $message; ?></li><?
            }
            ?></ul><?
            if (array_key_exists('div_class', $params))
            {
                ?></div><?
            }
        }

    }

    public function getMessagesWithField($field)
    {
        if (isset($this->_messagesWithField[$field]) and count($this->_messagesWithField[$field]))
            return $this->_messagesWithField[$field];
        return null;
    }

    public function getMessages()
    {
        if (count($this->_messages))
            return $this->_messages;
        return null;
    }

    public function getMessagesAll()
    {
        $messages = $this->_messages;
        foreach($this->_messagesWithField as $field => $value)
        {
            $messages = array_merge($messages, $value);
        }
        if (count($messages))
            return $messages;
        return null;
    }

    public function countMessages()
    {
        return count($this->_messages);
    }

    public function countMessagesWithFields()
    {
        return count($this->_messagesWithField);
    }


    /**
     * Есть ли сообзения об ошибках.
     *
     * @return bool
     */
    public function isErrors()
    {
        if ($this->countMessages() or $this->countMessagesWithFields())
        {
            return true;
        }
        return false;
    }

    /**
     * Были ли успешно сохранены данные.
     *
     *
     * @return boolean
     */
    public function isSaved()
    {
        return $this->_saved;
    }

    /**
     * Была ли попытка сохранения данных.
     *
     * @return null
     */
    public function isTrySave()
    {
        return $this->_trySave;
    }

    /**
     * Алиас метода isTrySave()
     *
     * @return null
     */
    public function isTry()
    {
        return $this->isTrySave();
    }

    /**
     * Установка флага попытки сохранения.
     *
     * @return Data
     */
    public function setTrySave()
    {
        $this->_trySave = true;
        return $this;
    }

    /**
     * Установка флага сохранения.
     *
     * @return Data
     */
    public function setSaved()
    {
        $this->_saved = true;
        return $this;
    }



//////////////////////////////////////////////////////////////////
///////////////////////////////     Методы интерфейса Countable
    public function count()
    {
        return count($this->_dataProcess);
    }


//////////////////////////////////////////////////////////////////
///////////////////////////////     Методы интерфейса ArrayAccess
    public function offsetExists($key)
    {
        return isset($this->_dataProcess[$key]);
    }
    public function offsetGet($key)
    {
        if (isset($this->_dataProcess[$key]))
            return $this->_dataProcess[$key];
        else
            return null;
    }

    public function offsetSet($key, $value)
    {
        $this->_dataProcess[$key] = $value;
    }
    public function offsetUnset($key)
    {
        unset($this->_dataProcess[$key]);
    }

    ////////////////////////////////////////////////////////////////
///////////////////////////////     Методы интерфейса Iterator
    // устанавливает итеретор на первый элемент
    public function rewind()
    {
        return reset($this->_dataProcess);
    }
    // возвращает текущий элемент
    public function current()
    {
        return current($this->_dataProcess);
    }
    // возвращает ключ текущего элемента
    public function key()
    {
        return key($this->_dataProcess);
    }

    // переходит к следующему элементу
    public function next()
    {
        return next($this->_dataProcess);
    }
    // проверяет, существует ли текущий элемент после выполнения мотода rewind или next
    public function valid()
    {
        return isset($this->_dataProcess[key($this->_dataProcess)]);
    }
/////////////////////////////
////////////////////////////////////////////////////////////////


}
