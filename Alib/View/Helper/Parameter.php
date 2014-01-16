<?php
/**
 * Возврат системных параметров для вида.
 *
 * @package
 * @category
 * @author      Andrey Ryzhov <webmaster@rzn.info>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 */
namespace Alib\View\Helper;
use Alib;
class Parameter extends Alib\View\HelperAbstract
{
    
    private $_path = '/viewfiles/';
    private $_pathFiles = '/viewfiles/';

    private $_subdoman = '';
    private $_module = 'www';

    private $_data = array();
    private $_keyCurrent = 'viewfiles';

    public function  __construct()
    {
//        $this->_subdoman = '';
        $reg = Alib\Registry::getInstance();
        
        $this->_data['design']     = $reg->get('design');
        $this->_data['subdomain']  = $reg->get('subdomain');
        $this->_data['version']    = $reg->get('version');
        $this->_data['module']     = $reg->get('module');
        $this->_data['domain']     = $this->_data['site']  = $reg->get('domain');

        
        $params = Alib\Params::getInstance();
        $this->_subdoman = $params->getSubdomanForFiles();
        
 //       $this->_data['viewfiles_path'] = $_SERVER['DOCUMENT_ROOT'] . $this->_path . $this->_data['design'];
        $this->_data['viewfiles_path'] = $params->getFilesPathForSystem(false) . $this->_path . $this->_data['design'];
        
/*        
 *      Статик домен пока один и он основной.
 * 
        $pars = lib\Params::getInstance();
        $postfix = $pars->getFilesDomainPostfix();
*/        
        
        $postfix = '';

        //$this->_data['viewfiles_common'] = $this->_path = 'http://' . $this->_subdoman . $this->_data['site'] . $postfix . $this->_pathFiles . $this->_data['design'];
        $this->_data['viewfiles_common'] = $this->_path = $params->getFilesPathForBrowser(false) . $this->_pathFiles . $this->_data['design'];

        $this->_data['root'] = $this->_path = 'http://' . $this->_subdoman . $this->_data['site'];

        
        $this->_data['files'] = 'http://' . $this->_subdoman . $this->_data['site'] . $postfix;
    }

    public function parameter($key = 'viewfiles', $module = null)
    {
        if ($module === null)
        {
            $this->_data['viewfiles'] = $this->_data['viewfiles_common'] . '/' . $this->_data['module'];
            $this->_data['viewfiles_path'] = $this->_data['viewfiles_path'] . '/' . $this->_data['module'];
            $this->_module = $this->_data['module'];
        }
        else
        {
            $this->_data['viewfiles'] = $this->_data['viewfiles_common'] . '/' . $module;
            $this->_data['viewfiles_path'] = $this->_data['viewfiles_path'] . '/' . $module;
            $this->_module = $module;
        }
        $this->_keyCurrent = $key;
        return $this;
    }
    
    public function _parameter($value = 'viewfiles')
    {
        return $this->_data[$value];
    }
    
    public function get($value = 'viewfiles')
    {
        return $this->_data[$value];
    }
 
    public function  __toString()
    {
        return $this->_data[$this->_keyCurrent];
    }
}


