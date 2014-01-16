<?php
/**
 * V.03
 * Обработка файла вида в специальной папке.
 * 
 * Вторым параметром может быть уточнее к пути специальных видов. По умолсанию к текстам писем.
 *
 * Версии:
 *   2012-11-13 Смена папки спец шаблонов по умолчанию.
 *   2011-09-23 Повышена стабильность. Вызов для изьятия параметров.
 *   2011-09-21 Добавлена передача и сохранение параметров.
 *   2011-09-09 Создание. Первое применение.
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
use Alib\Registry;
class RenderSpecial extends \Alib\View\HelperAbstract
{
    protected $_filePostix = '.phtml';
    protected $_file = '';
    protected $_folder = '';
    
    protected $_scriptPaths = '';
    
    protected $_path = '';
    protected $_params = '';

    
    public function __construct() 
    {
        $reg = Registry::getInstance();
        $dir = $reg->get('dir_modules');
        $module = $reg->get('module');
        $this->_path = $dir . '/' . $module . '/views/special/';
    }


    public function direct($file = null, $folder = 'email')
    {
        if (!$file)
            return $this;
        if (!$this->_scriptPaths)
            $this->_scriptPaths = $this->view->getScriptPaths();
        $this->_file = trim($file , '/') . $this->_filePostix;
        $this->_folder = $folder;
        
        $this->_params = '';
        
        return $this;
    }
    
    public function setParams($array)
    {
        $this->_params = $array;
        return $this;
    }

    public function getParam($key)
    {
        if (isset($this->_params[$key]))
            return $this->_params[$key];
        return null;
    }
    
    
    
    protected function _beforeObjectEcho()
    {
        $path = $this->_path . '/' . $this->_file;
        try
        {
            $this->view->setScriptPath($this->_path . $this->_folder);
            
            $this->_html = $this->view->render($this->_file);
            $this->view->setScriptPath($this->_scriptPaths);
        }
        catch (\Exception $e)
        {
            echo $e->getMessage();
        }
    }
}