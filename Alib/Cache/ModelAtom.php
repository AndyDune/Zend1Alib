<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 16.08.12
 * Time: 12:37
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Cache;
class ModelAtom extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 7200,                   // cache lifetime of 30 seconds
        'automatic_serialization' => false  // this is the default anyways
    );

    protected $_backendOptions = array(
        'hashed_directory_level'   => 2,
    );


    protected $_cacheSubdir = 'model-atom';

}