<?php
/*
 * Кеширование результатов работы произвольного объекта
 * Принимается объкт и имя запускаемого метода
 *
 * История
 *   2011-07-13 Устранена ошибка передачи параментров целевому методу.
 *   2011-07-12 Создан
 *
 */
namespace Alib;
class CacheObject
{
    protected $_object = null;
    protected $_methodName = null;
    protected $_className = null;
    protected $_parameters = array();
    
    protected $_cacheMode = 'object';

    protected $_prepareMethods = array();


    /**
     *
     * @param object $object объект
     * @param string $method_name Имя метода для запуска.
     */
    public function __construct($object, $method_name, $cacheMode = 'object')
    {
        if (is_string($object))
        {
            $this->_className  = $object;
            $this->_object     = new $object();
        }
        else
        {
            $this->_object     = $object;
            $this->_className  = get_class($object);
        }

        $this->_methodName = $method_name;
        $this->_cacheMode = $cacheMode;
    }

    /**
     * Двойственная природа.
     * 1. Если вызванный метод равен указанному при созданнии объекта - 
     *    происходит выборка данных. Из кеша или отрабатыается целевой объект.
     * 2. Сохраняется имя метода и его параметры для участия в формировании ключа
     *    кеша и для инициилизации целевого объекта.
     * 
     * @param string $name имя вызываемого метода
     * @param array $arguments аргументы
     * @return \Zend_Db_Table_Rowset 
     */
    public function __call($name, $arguments)
    {
        // Вызван кешируемый метод
        if ($name == $this->_methodName)
        {
            $this->_setParams($arguments);
            return $this->_get();
        }
        return $this->_prepare($name, $arguments);
    }
    
    
    
    /**
     * Перед запуском основного метода запроса может понадобиться установка параметров.
     * Подготовка к запросу. Эти данные используются для формирования ключа дял кеша.
     *
     * @param string $method_name имя метода
     * @param array $params неасоц. массив параметров метода
     * @param boolean $spec параметры содержат массивы
     * @return Db_Cache
     */
    protected function _prepare($method_name, $params = array())
    {
        $this->_prepareMethods[] = array(
            'method' => $method_name,
            'params' => $params
        );
        return $this;
    }


    /**
     * Установка параметров для целевого метода.
     * Любое колличесво параметров.
     * В порядке, в каком они будут переданы методу.
     *
     * @return Db_Cache
     */
    
    protected function _setParams($arguments)
    {
        $this->_parameters = $arguments;
/*
        if (func_num_args())
        {
            $this->_parameters = func_get_args();
        }
  */
        return $this;
    }    

    /**
     * Запрос результатов. Реальные либо из кеша.
     *
     * @return \Zend_Db_Table_Rowset
     */
    protected function _get()
    {
        $key = $this->_buildCacheKey();
        $data = $this->_checkCache($key);
        if ($data)
            return $data;

        $prepare = $this->_prepareMethods;
        if (count($prepare))
        {
            foreach($prepare as $value)
            {
                call_user_func_array(array($this->_object, $value['method']), $value['params']);
            }
        }

        $data = call_user_func_array(array($this->_object, $this->_methodName), $this->_parameters);
        $this->_storeCache($key, $data);
        return $data;
    }

    protected function _checkCache($key)
    {
        $cache = Cache::factory($this->_cacheMode);
        $data = $cache->load($key);
        return $data;
    }

    protected function _buildCacheKey()
    {
        $name = '';
        $name = $this->_className . '++' . $this->_methodName;
        $name .= '++' . serialize($this->_parameters);
        $name .= '++' . serialize($this->_prepareMethods);
  
/*        
        if ($this->_parameters)
        {
            $pars = array();
            foreach($this->_parameters as $value)
            {
                if (is_array($value))
                    $value = serialize($value);
                $pars[] = $value;
            }
            $name .= implode ('++', $pars);
        }

        if (count($prepare))
        {
            
            foreach($prepare as $value)
            {
                
                $name .= '-' . serialize($value);
                
                if ($value['spec'])
                {
                    foreach($value['params'] as $val)
                    {
                        if (is_array($val))
                        {
                            $val = implode("'", $val);
                        }
                        if (!$name) $name .= '-';
                            $name .= $val;
                    }
                }
                else
                {
                    $name .= implode('-', $value['params']);
                }
                $name .= '-' . $value['method'] . '-' . $name;
            }
        }
 */


        return md5($name);
    }

    protected function _storeCache($key, $data)
    {
        $cache = Cache::factory($this->_cacheMode);
        $cache->save($data, $key);
        return $this;
    }



}
