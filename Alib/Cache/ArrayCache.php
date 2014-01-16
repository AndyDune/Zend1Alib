<?php
/*
 * Общее кеширование.
 */
namespace Alib\Cache;
class ArrayCache extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 300,                   // cache lifetime of 30 seconds
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
                                    'hashed_directory_level'   => 2,
                                     );


    protected $_cacheSubdir = 'common';

}
