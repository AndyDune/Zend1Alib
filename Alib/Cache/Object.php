<?php
/*
 * Кеширование данных - результатов работы объектов.
 *
 * Версии:
 * 2011-07-12 Создан
 *
 */

namespace Alib\Cache;
class Object extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 3600,                   // cache lifetime of 30 seconds
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
                                    'hashed_directory_level'   => 3,
                                     );


    protected $_cacheSubdir = 'object';

}
