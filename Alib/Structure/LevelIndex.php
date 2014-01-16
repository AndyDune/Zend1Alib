<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 12.03.12
 * Time: 0:14
 */
namespace Alib\Structure;
class LevelIndex
{
    /**
     * @var Level
     */
    protected $_level;
    protected $_processed = false;

    protected $_html = '';
    protected $_data = '';

    public function __construct(Level $level)
    {
        $this->_level = $level;
    }

    public function process()
    {
        if ($this->_processed)
            return $this;
        $file = $this->_level->getPath() . '/index.php';
        if (is_file($file))
        {
            $application = array();
            ob_start();
            include($file);
            $this->_html = ob_get_clean();
            $this->_data = $application;
            $this->_processed = true;
        }
        return $this;
    }

    public function getHtml()
    {
        return$this->_html;
    }

    public function __get($key)
    {
        if (isset($this->_data[$key]))
            return $this->_data[$key];
        return null;
    }

}
