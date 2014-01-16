<?php
/*
 * Базовый класс для классов настроек кеширования под разные задачи.
 *
 * Версии:
 * 2011-02-16 Создан
 * 
 */
namespace Alib\Cache;
abstract class AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 300,                   // cache lifetime of 30 seconds
        'automatic_serialization' => false  // this is the default anyways
    );

    protected  $_backendOptions = array();

    /**
     * Поддиректория для кеширования.
     * @var string
     */
    protected $_cacheSubdir = 'common';

    protected $_frontend = 'Core';
    protected $_backend = 'File';


    /**
     * Изменение параметров адаптера зада для конкретного типа кеша.
     *
     * Изменяется патаметр cache_dir с добавление поддиректории для этого типа кеширования.
     *
     * @param array $array масив базовых параметров, которые надо изменить
     * @return arrray
     */
    public function formatBackendOptions($array)
    {
        foreach ($this->_backendOptions as $key => $value)
        {
            $array[$key] = $value;
        }
        if ($this->_cacheSubdir)
        {
            $directory = $array['cache_dir'] . '/' . $this->_cacheSubdir;
            if (!is_dir($directory))
            {
                $makeDir = new \Alib\Directory\Make($array['cache_dir'], true);
                $makeDir->setTargetDirectoryName($this->_cacheSubdir);
                $array['cache_dir'] = $makeDir->make();
            }
            else
                $array['cache_dir'] = $directory;
        }
        return $array;
    }

    /**
     * Изменение параметров адаптера лица для конкретного типа кеша.
     * @param array $array масив базовых параметров, которые надо изменить
     * @return arrray
     */
    public function formatFrontendOptions($array)
    {
        foreach ($this->_frontendOptions as $key => $value)
        {
            $array[$key] = $value;
        }
        return $array;
    }

    /**
     * Возвращает имя адаптера для лица
     * @return string
     */
    public function getFrontend()
    {
        return $this->_frontend;
    }
    
    /**
     * Возвращает имя адаптера для зада
     * @return string
     */
    public function getBackend()
    {
        return $this->_backend;
    }

}
