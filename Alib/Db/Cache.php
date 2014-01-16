<?php
/*
 * Кеширование результатов запросов в БД.
 * Работает как надстройка для классов доступа к базе данных.
 * Принимается объкт и имя запускаемого метода
 *
 * История
 *   2011-07-12 Кардинально доработан. Учитываемые мотоды в кешировании анализируются через __call
 *   2011-06-17 Параматр для кешируемого метода может быть массивом.
 *   2011-04-29 Ошибка сбора ключа !!
     2011-04-19 Повышена стабильность
 *   2011-04-18 Констрктор может принимать массив с параметрами для создания объекта запроса к базе.
 *   2011-04-17 Введен метод prepare() - вызов дополнительных методов.
 *   2011-04-16 Создан
 *
 */
namespace Alib\Db;
use Alib;
class Cache extends Alib\CacheObject
{

    protected $_cacheMode = 'Db';
    
    /**
     *
     * @param object|array $object объект доступа к БД
     * @param string $method_name Имя метода для запуска.
     */
    public function __construct($object, $method_name)
    {
        if (is_array($object))
        {
            //$name, $group, $realization
            if (!isset($object[0]) or !isset($object[1]))
            {
                throw new Exception('Нет обязательных параметров', 1001);
            }
            if (!isset($object[2]))
            {
                $object[2] = 'base';
            }
            $object = Factory::select($object[0], $object[1], $object[2]);
        }
        $this->_object     = $object;
        $this->_className  = get_class($object);
        $this->_methodName = $method_name;
    }
}