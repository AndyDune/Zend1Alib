<?php

/**
 * Ссылки на файлы видов: Стили, скрипты, картинки
 *
 * @author dune
 */

namespace Alib\View\Helper;
use Alib\Registry;
class ViewFiles extends \Alib\View\HelperAbstract
{
    
    private $_path = '/viewfiles/';
    private $_pathFiles = '/viewfiles/';

    private $_subdoman = '';
    private $_moduleBase = 'www';
    private $_moduleCurrent = 'www';

    
    private $_data = array();
    
    private $_viewPath = '';
    
   
    protected $_paremeters = null;


    protected $_htmlAdd = '';
    
    
    public function  __construct()
    {
//        $this->_subdoman = '';
        $reg = Registry::getInstance();
        
        $this->_data['design']  = $reg->get('design');
        $this->_data['version'] = $reg->get('version');
        $this->_data['module']  = $reg->get('module');
        $this->_data['domain']  = $reg->get('domain');

        $this->_moduleCurrent  = $reg->get('subdomain');

        $this->_subdoman = 'static.';
        
        $this->_viewPath = 'http://' . $this->_subdoman . $this->_data['domain'] 
                         . $this->_path . $this->_data['design'];
    }    
    
    public function direct($value = null, $module = null)
    {
        if ($module)
            $this->_moduleCurrent = $module;
        $this->_paremeters    = $value;
        return $this;
    }

    public function jQuery()
    {
        return '<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>';
    }

    public function file($file, $module = null)
    {
        if (!$module)
            $module = $this->_moduleCurrent;
        
        return $this->_viewPath . '/' . $module . '/' . $file;
    }
    
    public function css($file, $module = null, $fromEnd = false)
    {
        $file = 'css/' . $file . '.css';
        $html = '<link rel="stylesheet" href="'
             . $this->file($file, $module)
             .'" />';
        if ($fromEnd)
            $this->_htmlAdd .= $html;
        else
            $this->_html .= $html;
        return $this;
    }
    
    public function js($file, $module = null)
    {
        $file = 'js/' . $file . '.js';
        $this->_html .= '<script type="text/javascript" src="'
             . $this->file($file, $module)
             .'"></script>';
        return $this;
    }

    public function fileImage($file, $module = null)
    {
        $file = 'img/' . $file;
        return $this->file($file, $module);
    }
    
    
    protected function _beforeObjectEcho()
    {
        $str = '';
        $view_path = $this->_viewPath . '/' . $this->_moduleCurrent . '/css/';
        if ($this->_paremeters and is_array($this->_paremeters))
        {
            if (isset($this->_paremeters['css']))
            {
                if (is_array($this->_paremeters['css']))
                {
                    foreach($this->_paremeters['css'] as $value)
                    {
                        $str .= '<link rel="stylesheet" href="'
                             . $view_path
                             . $value
                             .'.css" />';
                    }
                }
            }
            if (isset($this->_paremeters['js']))
            {
                if (is_array($this->_paremeters['js']))
                {
                    foreach($this->_paremeters['js'] as $value)
                    {
                        $this->js($value, $this->_moduleCurrent);
                    }
                }
            }

        }
        $this->_html = $str . $this->_html . $this->_htmlAdd;
    }
    
    
}


