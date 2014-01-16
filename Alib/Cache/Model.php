<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 16.08.12
 * Time: 13:04
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Cache;
class Model extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 72000,                   // cache lifetime of 30 seconds
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
        'hashed_directory_level'   => 3,
    );


    protected $_cacheSubdir = 'model';

}
