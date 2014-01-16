<?php
/*
 * Общее кеширование.
 */
namespace Alib\Cache;
class Common extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 300,                   // cache lifetime of 30 seconds
        'automatic_serialization' => false  // this is the default anyways
    );

    protected $_backendOptions = array(
                                    'hashed_directory_level'   => 2,
                                     );


    protected $_cacheSubdir = 'common';
    
}
