<?php
/**
 * Вывод сообщения, ранее сохраненого в сессии.
 *
 * Версии:
 *   2011-08-04 Работа с дополнительными сообщениями.
 * 
 * @package
 * @category
 * @author      Andrey Ryzhov <master@rznw.ru>
 * @author      $Author: $
 * @version     $Rev: $
 * @since       $Date: $
 * @link        $URL: $
 */
namespace Alib\View\Helper;
use Alib;
class Message extends \Alib\View\HelperAbstract
{
    /**
     *
     * @var Dlib_Session_Abstract
     */
    protected $_object = null;

    protected $_codes = array();
    
    protected $_template    = '<div class="message">?</div>';
    protected $_templateBad = '<div class="message error-message">?</div>';

    protected $_templateAddMessagePrefix  = '<ul class="message-add">';
    protected $_templateAddMessageLine = '<li><span>{code}</span>{text}</li>';
    protected $_templateAddMessagePostfix = '</ul>';
    
    
    protected $_code = null;
    protected $_text = null;
    protected $_textCode = '';
    protected $_good = null;

    protected $_success = false;
    
    protected $_quality = null;
    
    protected $_messages = null;
    protected $_messageAdd = null;

    public function __construct()
    {
        $object = new Alib\Session\Message();
        if ($object->try < 2)
        {
            $this->_quality = $object->quality;
            $this->_code = $object->code;
            $this->_text = $object->text;
            $this->_success = $object->success;
            
            $this->_messageAdd = $object->message_add;
            
            if ($this->_code > 0)
                $this->_good = true;
            else if ($this->_code !== null)
                $this->_good = false;
        }
        /*
        $dir = Alib\Registry::get('dir');
        $translator = new \Zend_Translate('array', $dir . '/modules/Www/configs/errors.php');
        $adaptor = $translator->getAdapter();
        $this->_messages = $adaptor->getMessages();
        */
        $object->clean();
    }


    public function message($codes = null, $template = '', $template_bad = '')
    {
        if ($template)
            $this->_template = $template;
        if ($template_bad)
            $this->_templateBad = $template_bad;

        if ($this->_code !== null 
            and is_array($codes)
            and isset($codes[$this->_code])
            )
        {
            $this->_textCode = $codes[$this->_code];
        }
        else if ($this->_code !== null and isset($this->_messages[$this->_code]))
        {
            $this->_textCode = $this->_messages[$this->_code];
        }
        return $this;
    }
    
    public function getQuality()
    {
        return $this->_quality;
    }

    public function isSuccess()
    {
        return $this->_success;
    }
    
    public function add()
    {
        $str = '';
        $array = $this->_messageAdd;
        if (is_array($array) and count($array))
        {
            $str .= $this->_templateAddMessagePrefix;
            foreach($array as $key =>$value)
            {
                $line = str_replace('{code}', $key, $this->_templateAddMessageLine);
                $line = str_replace('{text}', $value, $line);
                $str .= $line;
            }
            $str .= $this->_templateAddMessagePostfix;
        }
        return $str;
    }
    

    public function  __toString()
    {
        if ($this->_textCode)
            $text = $this->_textCode;
        else if ($this->_text)
            $text = $this->_text;
        else
            $text = false;
        if ($text)
            if ($this->_good)
                return str_replace('?', $text, $this->_template);
            else
                return str_replace('?', $text, $this->_templateBad);
        return '';
    }
}

