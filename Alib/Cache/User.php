<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 03.05.12
 * Time: 14:54
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Cache;
class User extends AbstractClass
{
    protected $_frontendOptions = array(
        'lifetime' => 36000,                   // cache lifetime
        'automatic_serialization' => true  // this is the default anyways
    );

    protected $_backendOptions = array(
        'hashed_directory_level'   => 3,
        'cache_dir' => 'cache',
    );

    protected $_cacheSubdir = '';

    protected $_cacheDir = 'user';

    public function __construct()
    {
        $reg = \Alib\Registry::getInstance();
        $version = $reg->get('version');
        $cacheDir =  $version['data'] . '/cache';


        $auth = \Alib\Auth::getInstance();
        $userData = $auth->getIdentity();
        if (!$userData)
            return ;

        $makeDir = new \Alib\Directory\Make($cacheDir, true);
        $makeDir->setLevelsFromTargetDirectoryName(3);
        $makeDir->setTargetDirectoryName($this->_cacheDir . '/' . $userData['id']);
        $this->_backendOptions['cache_dir'] = $makeDir->make();
    }

}
