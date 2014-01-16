<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 18.12.12
 * Time: 13:01
 *
 *
 */
namespace Alib\Cache;
class System extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 3600000,
        'automatic_serialization' => false
    );

    protected $_backendOptions = array(
        'hashed_directory_level'   => 0,
    );


    protected $_cacheSubdir = 'system';

}