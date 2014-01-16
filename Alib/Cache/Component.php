<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 16.08.12
 * Time: 13:17
 * To change this template use File | Settings | File Templates.
 */

namespace Alib\Cache;
class Component extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 3600,                   // cache lifetime of 30 seconds
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
        'hashed_directory_level'   => 3,
    );


    protected $_cacheSubdir = 'component';

}