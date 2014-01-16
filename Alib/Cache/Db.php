<?php
/*
 * Кеширование данных таблицы из спец. классов.
 *
 * Версии:
 * 2011-04-16 Создан
 *
 */

namespace Alib\Cache;
class Db extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 36000,                   // cache lifetime of 30 seconds
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
                                    'hashed_directory_level'   => 2,
                                     );


    protected $_cacheSubdir = 'db';

}