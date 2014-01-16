<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 15.05.12
 * Time: 15:57
 *
 * Хранитель одной единицы информации.
 *
 * Шаблон для классов
 */
namespace Alib\Record\AbstractClass;
use Alib\Exception;
use Alib\Record as RecordSpace;
abstract class Record implements \ArrayAccess
{
    use \Alib\System\Traits\BuildClassName;
    /**
     * Первичный ключ записи.
     * Если заполнен - запись существует.
     *
     * @var null
     */
    protected $_id = null;

    /**
     * Заголовок записи.
     * Если массив - заголовков несколько для единсвенного и множетсвенного числа.
     *   ['one'  => <единсвенное число>,
     *    'many' => <множесвенное>
     *   ]
     *
     *
     * @var string|array
     */
    protected $_title = '';


    /**
     * Массив заголовков полей
     *
     * @var array
     */
    protected $_fieldTitles = [];


    /**
     * Имя столбца, первичного ключа.
     *
     * @var string
     */
    protected $_idField = 'id';


    protected $_titleField = 'title';


    /**
     * Массив связей
     *
     * <имя связи в пределай этого класса> => [
     *            'type'   => <one-to-many|many-to-many|many-to-one>,
     *            'record' => <запись>
     *            'key'    => <значение ключа для выборки сваязанных>
     *
     *                  ]
     *
     *  <записьт> => <таблица,группа,реализация,модуль|таблица,группа,модуль>
     *               без указания реализации - это реализация как и в текущей записи.
     *
     * @var array
     */
    protected $_relation = array();

    /**
     * Таблица, из которой выбирается запись.
     * Для основной информации записи, ибо может быть доп. информация из др. таблиц.
     *
     * @var \Alib\Db\Table
     */
    protected $_table = null;


    protected $_groupName   = null;
    protected $_tableName   = null;

    protected $_module   = null;

    protected $_realization = 'base';
    /**
     * Данные записи, подготовленные для вставки в базу.
     *
     * @var array
     */
    protected $_data = array();


    protected $_aliases = array();


    /**
     * Данные записи, выбранные и не измнненные.
     *
     * @var array
     */
    protected $_dataRetrieved = array();


    /**
     * Данные для автоматического формирования формы редактирования записи в админке.
     *
     * Может быть вшито, либо динамически вставляться из конфигов.
     *
     * @var array
     */
    protected $_dataFormat = array();

    /**
     * Список полей для вывода в списке.
     *
     * @var array
     */
    protected $_dataListFormat = array();

    /**
     * Список полей, которые участвуют в фильтрации.
     *
     * @var array
     */
    protected $_filterFormat = [];


    /**
     * @var \Alib\Record\Data
     */
    protected $_dataObject;

    protected $_test = false;

    /**
     *
     * @var \Alib\EventManager
     */
    protected $_events = null;

    public function __construct($realization = 'base')
    {
        if (!$realization)
        {
            $realization = 'base';
        }
        $this->_realization = $realization;
        $this->_dataObject = new RecordSpace\Data($this);
        $this->init();
    }

    public function clear()
    {
        $this->_dataRetrieved = $this->_data = [];
        $this->_id = null;
        $this->_dataObject = new RecordSpace\Data($this);
        return $this;
    }

    public function getModuleName()
    {
        if ($this->_module)
            goto before_exit;

        $className = get_class($this);
        $parts = explode('\\', $className);
        $this->_module = $parts[1];

        before_exit:
        return $this->_module;
    }


    public function init()
    {

    }

    /**
     * Не использовать метод для выборки имиени группы.
     * Использовать getGroupName
     *
     * @return null
     */
    public function getGroup()
    {
        return $this->_groupName;
    }

    /**
     * Выбрать имя группы
     *
     * @return string
     */
    public function getGroupName()
    {
        return $this->_groupName;
    }

    /**
     * Выбрать имя таблицы.
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_tableName;
    }


    /**
     * Возвращает
     * nets,trade,base,Shops
     *
     *
     */
    public function getCreationString()
    {
        return $this->_tableName . ',' . $this->_groupName . ','
               . $this->_realization . ',' . $this->getModuleName();

    }
    
    public function setEventManager($events)
    {
        $this->_events = $events;
        return $this;
    }

    public function events()
    {
        if (!$this->_events)
        {
            throw new \Alib\Exception('Не установлен менеджер событий', 1);
        }
        return $this->_events;
    }

    public function test($test = true)
    {
        $this->_test = $test;
        return $this;
    }

    public function getRealization()
    {
        return $this->_realization;
    }

    public function setTable(\Alib\Db\Table $table)
    {
        $this->_table = $table;
        return $this;
    }

    /**
     * Вернуть заголовок записи.
     *
     * @param string $type
     * @return array|string
     */
    public function getTitle($type = 'one')
    {
        if (is_array($this->_title) and array_key_exists($type, $this->_title))
            return $this->_title[$type];
        return $this->_title;
    }


    /**
     * Выбрать заголовок выбранных данных.
     * Может состоять из одного поля, композиции полей.
     * Возможно включение данных из связной записи.
     *
     * @return int|null
     */
    public function getDataTitle()
    {
        return $this->{$this->_titleField};
    }


    /**
     * Выбрать заголвок для поля или весть массив заголовков.
     *
     * @param null $field
     * @return array|null если null возвращается весь массив
     */
    public function getFieldTitle($field = null)
    {
        if (!$field)
            return $this->_fieldTitles;
        if (array_key_exists($field, $this->_fieldTitles))
            return $this->_fieldTitles[$field];
        return $field;
    }

    /**
     * Возврат объекта записи.
     *
     * @param $name
     * @param $group
     * @param string $realization
     * @param string $module
     * @param null $id
     * @return Record
     */
    public function getRecord($name = null, $group = null, $realization = null, $module = null, $id = null)
    {
        if (!$name)
            $name = $this->getCreationString();
            //$this->_realization = $realization;
        $record = $this->_getRecordObject($name, $group, $realization, $module);
        //$record = new $name($realization);
        //$record->initTable($realization);
        if ($id)
        {
            $record->retrieve($id);
        }
        return $record;
    }

    public function getRecordInGroup($name, $id = null)
    {
        $group = $this->_groupName;
        $realization = $this->_realization;
        $realization = $this->_realization;
        $module = $this->getModuleName();
        $record = $this->_getRecordObject($name, $group, $realization, $module);
        //$record = new $name($realization);
        //$record->initTable($realization);
        if ($id)
        {
            $record->retrieve($id);
        }
        return $record;

    }

    /**
     * Возврат модели из записи.
     *
     * @param $name
     * @param null $module
     * @param string $type
     * @return mixed
     */
    public function getModel($name = null, $module = null, $type = null, $realization = null)
    {
        if (!$name)
            $name = $this->_groupName;
        if (!$realization)
            $realization = $this->_realization;
        if (!$realization)
            $realization = 'base';
        if (!$type)
            $type = 'data';

        if (!$module)
        {
            //$reg = \Alib\Registry::getInstance();
            //$module = $reg->get('module');
            $module = $this->getModuleName(); // Модель текущей
        }
        return \Alib\Model\Factory::getModel($name, $module, $type, $realization);
    }


    protected function _delete($id)
    {
        if ($id)
            $clear = false;
        else
        {
            $clear = true;
            $id = $this->getId();
        }
        if (!$id)
            return false;
        $tableDb = $this->_table->getTableDbObject();
        $tableDb->deleteWithId($id);
        if ($clear)
            $this->clear();
        return true;
    }

    /**
     * Удаление записи.
     *
     * @return bool флаг успеха
     */
    final public function delete($id = null)
    {
        $result = $this->_delete($id);
        if ($result)
        {
            $this->events()->trigger('record.delete', $this);
        }
        return $result;
    }


    public function setAlias($alias, $field)
    {
        $this->_aliases[$alias] = $field;
        return $this;
    }

    /**
     * Выбрать масив параметров для вывода автоматического списка.
     *
     * @param null $mode режим
     * @return array|null
     */
    public function getDataListFormat($mode = null)
    {
        if (count($this->_dataListFormat))
            return $this->_dataListFormat;
        return null;
    }

    public function setDataListFormat($data)
    {
        $this->_dataListFormat = $data;
        return $this;
    }

    /**
     * Выбрать массив для настройки отображения фильтра.
     *
     * @return array|null
     */
    public function getFilterFormat()
    {
        if (count($this->_filterFormat))
            return $this->_filterFormat;
        return null;
    }



    public function getRelations($key = null)
    {
        if (count($this->_relation))
        {
            if ($key)
            {
                if(array_key_exists($key, $this->_relation))
                {
                    return $this->_relation[$key];
                }
                throw new \Alib\Exception('Связь не существует: ' . $key, 1);

            }
            return $this->_relation;
        }
        return null;
    }

    public function setRelations($data)
    {
        $this->_relation = $data;
        return $this;
    }



    public function getDataFormat($field = null, $key = null)
    {
        if (!count($this->_dataFormat))
            return null;
        if (!$field)
            return $this->_dataFormat;
        if (!isset($this->_dataFormat[$field]))
            return null;
        if (!$key)
            return $this->_dataFormat[$field];
        if (!isset($this->_dataFormat[$field][$key]))
            return null;
        return $this->_dataFormat[$field][$key];
    }

    public function setDataFormat($data, $mode = null)
    {
        if (is_array($data))
            $this->_dataFormat = $data;
        return $this;
    }

    /**
     * Возврат только отфильтрованных форматов данных по опред условиям.
     *
     * Без условий - аналог getDataFormat без ключей.
     *
     * @param $data
     * @param null $mode
     * @return array
     */
    public function getDataFormatFiltered($filter = null)
    {
        $result = [];
        if (!$filter or !is_array($filter))
        {
            $res = $this->getDataFormat();
            if (!$res)
                goto b_e;
        }
        if (array_key_exists('class', $filter))
        {
            foreach($this->_dataFormat as $field => $value)
            {
                if (isset($value['class']) and $value['class'] == $filter['class'])
                {
                    $result[$field] = $value;
                }
            }
        }

        b_e:
        return $result;
    }


    /**
     * @return \Alib\Db\Table
     */
    public function getTable()
    {
        return $this->_table;
    }


    public function getDbFieldMetaData($field = null)
    {
        $objectDb = $this->_table->getTableDbObject();
        $info = $objectDb->info();
        $info = $info['metadata'];
        if (!$field)
            return $info;
        if (isset($info[$field]))
            return $info[$field];
        return null;
    }


    public function initTable($realization = 'base')
    {
        if (!$this->_groupName or !$this->_tableName)
            throw new Exception('Не установлены обязательные переменные: $_tableName или $_groupName' );
        $group = \Alib\Db\Factory::group($this->_groupName, $realization);
        $this->_table = $group->getTable($this->_tableName);
        return $this;
    }


    /**
     * Инициилизация данных с удалением id
     *
     * @param $data
     * @return Record
     */
    public function setData($data = null, $unsetId = true)
    {
        //echo 'record-setData<br>'; // todo стереть это
        if ($data)
        {
            if ($unsetId)
                unset($data[$this->_idField]);

            $this->_data = $data;
        }
        $this->_dataObject->init();
        return $this;
    }


    /**
     * Инициилизация данный из массива.
     * Без отдельного запроса в базу (retrieve)
     *
     * @param $data
     * @return Record
     * @throws \Alib\Exception
     */
    public function initFromData($data, $id = null)
    {
        if (is_array($id))
        {
            $params = $id;
            if (isset($params['id']))
                $id = $params['id'];
            else
                $id = null;
        }
        else
            $params = [];

        if (isset($params['prefix']) and $params['prefix'])
        {
            $processed = [];
            foreach($data as $key => $value)
            {
                $pos = strrpos($key, $params['prefix']);
                if ($pos === 0)
                {
                     $processed[substr($key, strlen($params['prefix']))] = $value;
                }
            }
            $data = $processed;
        }

        if (!$id)
            $id = $this->_idField;
        if (!array_key_exists($id, $data))
            throw new Exception('В массиве отсутствует ключевое поле: ' . $id, 1);
        $this->_id = $data[$id];
        unset($data[$id]);
        foreach($data as $key => $value)
        {
            if (array_key_exists($key, $this->_aliases))
            {
                $data[$this->_aliases[$key]] = $value;
                //unset($key); TODO не понятно зачем стирал
            }
        }
        $this->_dataRetrieved = $this->_data = $data;
        $this->_buildEnvironment();
        $this->_dataObject->init();
        return $this;
    }


    public function getData()
    {
        return $this->_data;
    }

    /**
     * Выбрать имя поля, которое является первичным ключем.
     *
     * @return string
     */
    public function getPrimaryFieldName()
    {
        return $this->_idField;
    }


    /**
     * Возврат оригинальных данных после retrieve.
     *
     * @return array
     */
    public function getDataRetrieved($key = null)
    {
        if (!$key)
            return $this->_dataRetrieved;
        if (array_key_exists($key, $this->_dataRetrieved))
            return $this->_dataRetrieved[$key];
        return null;
    }


    /**
     * Возврат id записи, или Null, если записи нет.
     *
     * @return null|integer
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Возвращает имя первичного ключа.
     *
     * @return null|integer
     */
    public function getIdFieldName()
    {
        return $this->_idField;
    }



    public function retrieve($id)
    {
        $this->_dataRetrieved = $this->_data = array();
        $this->_id = null;
        if (!$id)
            goto before_return;
        $tableDb = $this->_table->getTableDbObject();
        $data = $tableDb->get($id);
        if (!$data)
            goto before_return;
        $this->_dataRetrieved = $this->_data = $data->toArray();
        $this->_id = $id;
        $this->_buildEnvironment();
        $this->_dataObject->init();
        before_return:
        return $this;
    }

    public function retrieveLight($id)
    {
        $this->_dataRetrieved = $this->_data = array();
        $this->_id = null;
        if (!$id)
            goto before_return;
        $tableDb = $this->_table->getTableDbObject();
        $data = $tableDb->get($id);
        if (!$data)
            goto before_return;
        $this->_dataRetrieved = $this->_data = $data->toArray();
        $this->_id = $id;
        before_return:
        return $this;

    }

    public function retrieveWithField($value, $field = 'id')
    {
        $this->_dataRetrieved = $this->_data = array();
        $this->_id = null;
        if (!$value)
            goto before_return;
        $tableDb = $this->_table->getTableDbObject();
        $data = $tableDb->getWithStringField($value, $field);
        if (!$data)
            goto before_return;
        $this->_data = $data->toArray();
        $this->_id = $this->_data[$this->_idField];
        unset($this->_data[$this->_idField]);
        $this->_dataRetrieved = $this->_data;
        $this->_buildEnvironment();
        $this->_dataObject->init();
        before_return:
        return $this;
    }


    /**
     * Метод, который надо перегрузить в доченрнем классе для инициилизации окружения для записи.
     * Запускается в конце метода retrieve()
     *
     */
    protected function _buildEnvironment()
    {

    }


    /**
     * Запуск перед сохранением данных и перед проверкой.
     *
     *
     *
     * @return null
     */
    protected function _executeBeforeSave()
    {

    }


    /**
     * Выполнение действий после первичногно сохранения записи.
     * Важно, если нужно использовать существующий ID.
     *
     * Запускается метод модели или записи с перелачей ему необходимых данных в первом параметре и
     * текущего объекта записи во втором.
     *
     * @param $data Данные, которые порлучены от постобработки объектов типов.
     * @return null|array если массив - сохранить данные из него в базу
     */
    protected function _executeAfterSave($data)
    {
        return $data;
    }

    /**
     * Автоматически запускается непосредственно перед обновлением записи в базе.
     * Метод предназначен для перегрузки в дочерних коассах.
     *
     * @return Record
     */
    protected function _beforeUpdate()
    {
        return $this;
    }

    /**
     * Автоматически запускается непосредственно перед вставкой новой записи в базу.
     * Метод предназначен для перегрузки в дочерних коассах.
     *
     * @return Record
     */
    protected function _beforeInsert()
    {
        return $this;
    }

    /**
     * Автоматически запускается непосредственно перед вставкой новой записи в базу.
     * Метод предназначен для перегрузки в дочерних коассах.
     *
     * @return Record
     */
    protected function _afterInsert()
    {
        return $this;
    }



    public function getDataObject($reInit = false)
    {
        if ($reInit)
            $this->_dataObject->init();
        return $this->_dataObject;
    }


    /**
     * Проверка даннх без сохранения их.
     *
     * @param null $data
     * @param bool $processData
     * @return bool результат проверки данных
     */
    public function check($data = null, $processData = false)
    {
        if ($data and !is_array($data))
        {
            $std  = new \Alib\ArrayClass\StdClass($data);
            $data = $std->getArray();
        }


        if ($data and !$this->getId())
        {
            $primaryName = $this->getPrimaryFieldName();
            if (isset($data[$primaryName]) and $data[$primaryName])
            {
                $this->retrieve($data[$primaryName]);
            }
        }
        $this->setData($data);

        if (is_array($processData))
        {
            $this->_dataObject->checkOnlyFields($processData);
        }

        if (!$this->_dataObject->ready())
            return false;
        return true;

    }


    /**
     * @param null $data
     * @return \Alib\Db\TableDb|int|null
     */
    public function save($data = null, $processData = false)
    {
        if ($data and !is_array($data))
        {
            $std  = new \Alib\ArrayClass\StdClass($data);
            $data = $std->getArray();
        }


        if ($data and !$this->getId())
        {
            $primaryName = $this->getPrimaryFieldName();
            if (isset($data[$primaryName]) and $data[$primaryName])
            {
                $this->retrieve($data[$primaryName]);
            }
        }

        $this->setData($data);

        $this->events()->trigger('record.before-save', $this);
        if ($this->_dataObject->isErrors())
            return null;


        if (!$processData)
            goto no_process;

        if (is_array($processData))
        {
            $this->_dataObject->checkOnlyFields($processData);
        }

        if (!$this->_dataObject->ready())
            return null;
        $this->_data = $this->_dataObject->getData();

        //\Alib\Test::pr($this->_data, 1);

        no_process:

        if ($this->_test)
            return $this->_id;



        $tableDb = $this->_table->getTableDbObject();
        if ($this->_id)
        {
            $this->_beforeUpdate();
            $result = $tableDb->updateWithId($this->_data, $this->_id);
        }
        else
        {
            $this->_beforeInsert();
            $result = $this->_id = $tableDb->insert($this->_data, true);
            $this->_afterInsert();
        }
        $result = true;

        if ($result)
        {
            $this->retrieveLight($this->getId());
            $data = [];
            if ($processData)
            {
                $data = $this->_dataObject->commit();
            }

            $data = $this->_executeAfterSave($data);
            if ($data and is_array($data) and count($data))
            {
                $result = $tableDb->updateWithId($data, $this->_id);
            }
        }

        //$this->retrieve($this->getId()); // todo Разобраться о необходимости ретрива заноно. Предполагаю делать где-то еще.
        $this->events()->trigger('record.saved', $this);
        return $result;
    }


    public function saveQuickly($data = null)
    {
//        \Alib\Test::pr($data);
        if ($data and !$this->getId())
        {
            $primaryName = $this->getPrimaryFieldName();
            if (isset($data[$primaryName]) and $data[$primaryName])
            {
                $this->retrieve($data[$primaryName]);
            }
        }
        //$this->setData($data);

        if ($this->_test)
            return $this->_id;

        $tableDb = $this->_table->getTableDbObject();
        if ($this->_id)
        {
            //$this->_beforeUpdate();
            $result = $tableDb->updateWithId($data, $this->_id);
        }
        else
            $result = false;

        $this->events()->trigger('record.save-quickly', $this);
        return $result;
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->_data[] = $value;
        }
        else
        {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->_data[$offset]))
            return $this->_data[$offset];
        else if ($offset == $this->_idField)
            return $this->getId();
        return null;
    }


    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function __get($name)
    {
        return $this->offsetGet($name);
        if (isset($this->_data[$name]))
            return $this->_data[$name];
        return null;
    }

    public function __call($name , $arguments)
    {
        $parts = explode('_', $name);
        if(count($parts) != 2)
            goto b_e;

        $type = strtolower($parts[0]);
        switch ($type)
        {
            case 'datatype':
                if (!is_array($arguments) or !isset($arguments[0]))
                    throw new \Alib\Exception('Не передан обязательный параметр', 1);
                $dataType = $this->getDataObject()->getDataTypeObject($arguments[0]);
                array_shift($arguments);
                //\Alib\Test::pr($arguments, 1);
                return call_user_func_array([$dataType, $parts[1]], $arguments);
            break;
        }

        b_e:
        return null;

    }


    public function __toString()
    {
        ob_start();
        echo '<pre>';
        print_r($this->_data);
        echo '</pre>';
        return ob_get_clean();
    }

    public function toArray()
    {
        return $this->_data;
    }

}
