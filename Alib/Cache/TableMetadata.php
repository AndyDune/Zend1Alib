<?php
/* 
 * Кеширование метаданных таблицы
 *
 * Версии:
 * 2011-02-16 Создан
 * 
 */
namespace Alib\Cache;
class TableMetadata extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 36000,                   // cache lifetime of 30 seconds
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
                                    'hashed_directory_level'   => 0,
                                     );


    protected $_cacheSubdir = 'table_metadata';

}


