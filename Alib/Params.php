<?php
/*
 * Шенеральные параметры для сайта
 * 
 *
 *
 * Применение: 
               use rzn\lib\www as lib;
               $pars = lib\Params::getInstance();
               $postfix = $pars->getFilesDomainPostfix();

 *
 */
namespace Alib;
class Params
{
    
    private $_pathFilesForBrowser = '/data';
    private $_pathFilesForSystem = '/data';
    
    
    protected $_filesDomainPostfix = '';
    
    protected $_reg = null;
    
    protected $_domain = '1rzn.ru';
    protected $_subdomanFiles= '';

    static protected $instance = null;
    
    /**
     *
     * @return rzn\lib\www\Params 
     */
    static function getInstance()
    {
        if (static::$instance == null)
        {
            static::$instance = new static();
        }
        static::$instance->_reg = Registry::getInstance();
        return static::$instance;
    }
    
    protected function __construct()
    {
        $reg = Registry::getInstance();
        $site = $reg->get('option_site');
        $this->_filesDomainPostfix = $site['files']['domain_postfix'];
        
        $this->_domain    = $reg->get('domain');
        
        $siteOption    = $reg->get('site');
        if ($siteOption['subdomain_for_static'])
        {
            $this->_subdomanFiles = rtrim($siteOption['subdomain_for_static'], '.') . '.';
        }
        $this->_pathFilesForSystem = $_SERVER['DOCUMENT_ROOT'] . $this->_pathFilesForSystem;
        $this->_pathFilesForBrowser = 'http://' . $this->_subdomanFiles . $this->_domain . $this->_pathFilesForBrowser;
        
    }

    public function getFilesDomainPostfix()
    {
        return $this->_filesDomainPostfix;
    }

    public function get($key)
    {
        return $this->_reg->get($key);
    }
    
    public function set($key, $value)
    {
        return $this->_reg->set($key, $value);
    }

    public function getSubdomanForFiles()
    {
        return $this->_subdomanFiles;
    }

    public function getFilesPathForSystem($slash = true)
    {
        if ($slash)
            $postfix = '/';
        else
            $postfix = '';
        return $this->_pathFilesForSystem . $postfix;
    }
    
    
    public function getFilesPathForBrowser($slash = true)
    {
        if ($slash)
            $postfix = '/';
        else
            $postfix = '';
        return $this->_pathFilesForBrowser . $postfix;
    }
    
    
    
}