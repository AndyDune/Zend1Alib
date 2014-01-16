<?php
/*
 * Фабрика объектов для кеширования.
 *
 * Версии:
 *
 * 2011-04-30 Конфигурация из ini файла. Дополнительная папка.
 * 2011-02-16 Создан
 *
 */
namespace Alib;
class Cache
{
//    public static $allow = true;
    public static $allow = false;
    
    public static $frontendOptions = array(
        'automatic_cleaning_factor' => 0,
        'ignore_user_abort' => true,
        'caching'  => true,
        'lifetime' => 30,                   // cache lifetime of 30 seconds
        'automatic_serialization' => false  // this is the default anyways
    );

    public static $backendOptions = array(
                                    'cache_dir' => '/cache/',
                                    'hashed_directory_level' => 2,
                                    'hashed_directory_perm' => 0777,
                                    'cache_file_perm' => 0777
                                         );

    /**
     * Корневая папка для кеша разных типов.
     *
     * @var string
     */
     static protected $_cacheDir = 'cache';

     static protected $_dataNotSet = true;



                                         /**
     * Массив ссылок на объекты 
     *
     * @var array
     */
    private static $_instance = array();

    /**
     * Возвоат объекта для проведение кеширования. На основе передаваемого параметра.
     * Объекты заново не создаются - возвращаются уже созданные с ключем.
     *
     * @param string $name
     * @return \Zend_Cache_Core|\Zend_Cache_Frontend
     */
    static function factory($name = 'Common')
    {
        if (self::$_dataNotSet)
        {
            self::$_dataNotSet = false;
            self::_setData();
        }
        $name = ucfirst($name);
        if (isset(self::$_instance[$name]))
            return self::$_instance[$name];

        $class_name = 'Alib\\Cache\\' . $name;
        //$class_name = 'Cache_' . $name;
        $object = new $class_name();


        $frontendOptions = self::$frontendOptions;
        $backendOptions = self::$backendOptions;
        $backendOptions['cache_dir'] = self::$_cacheDir;

        // Прпускаем базовые массивы через индивидуальные класы
        $backendOptions = $object->formatBackendOptions($backendOptions);
        $frontendOptions = $object->formatFrontendOptions($frontendOptions);

        //\Alib\Test::pr($backendOptions, 1);

        $frontend = $object->getFrontend();
        $backend = $object->getBackend();

        
        $frontendOptions['caching'] = self::$allow;
        return self::$_instance[$name] = \Zend_Cache::factory($frontend, $backend, $frontendOptions, $backendOptions);
    }

    static protected function _setData()
    {
        $reg = Registry::getInstance();
        $cache = $reg->get('cache');
        $version = $reg->get('version');
        self::$allow = $cache['allow'];
        self::$_cacheDir = $version['data'] . '/cache/';
    }


}
