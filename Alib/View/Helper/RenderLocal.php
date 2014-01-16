<?php
/**
 * V.04
 * 
 * Обработка файла вида от текущей папки.
 * 
 * Вторым параметром болжен быть обязательно абсолютный путь к текущей папке.
 *
 * Версии:
 *   2011-09-23 Добавлены собственные параметры
 *   2011-09-23 Внесена возможность восстановления пути к скримтам restoreScriptPath().
 *              Это важно при рендеринге шаблонов в стандартном расположении из локально расположенного скрипта.
 *   2011-09-08 Создание. Первое применение.
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
class RenderLocal extends \Alib\View\HelperAbstract
{
    protected $_filePostix = '.phtml';
    protected $_file = '';
    protected $_path = '';
    
    protected $_scriptPaths = '';
    
    protected $_params = '';

    public function direct($file = null, $path = null)
    {
        if (!$file)
            return $this;
        if (!$path)
        {
            throw new \Alib\Exception('Не передан обязательный второй параметр, путь к текущей папке.');
        }
        if (!$this->_scriptPaths)
            $this->_scriptPaths = $this->view->getScriptPaths();
        $this->_file = trim($file , '/') . $this->_filePostix;
        $this->_path = $path;
        
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
    
    public function restoreScriptPath()
    {
        $this->view->setScriptPath($this->_scriptPaths);
        return $this;
    }    
    
    protected function _beforeObjectEcho()
    {
        $path = $this->_path . '/' . $this->_file;
        try
        {
            $this->view->setScriptPath('/');
            $this->_html = $this->view->render($path);
            $this->view->setScriptPath($this->_scriptPaths);
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }

    
}
