<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 08.10.12
 * Time: 18:55
 * To change this template use File | Settings | File Templates.
 */

namespace Alib\Record\DataType;
use Alib\Record;
use Alib\Exception;
class ReferenceStructure extends Record\DataType
{
    const BAD_DATA = 'BAD_DATA_REF';

    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = array(
        self::BAD_DATA    => "Неверные данные.",
    );


    protected $_recordToRelateName = '';

    /**
     * @var \Alib\Record\AbstractClass\Record
     */
    protected $_module = 'www';

    protected $_node = null;

    public function _processReady()
    {
        $result = true;

        return $result;
    }

    public function setModule($module = 'www')
    {
        $this->_module = $module;
        return $this;
    }

    public function setNode($node)
    {
        $this->_node = $node;
        return $this;
    }

    public function getList()
    {
        $structure = \Alib\Structure::getInstance();
        $result = $structure->getReference($this->_module, $this->_node);
        if ($result)
            return $result['data'];
        return null;
    }

    public function getFormatedValue()
    {
        $structure = \Alib\Structure::getInstance();
        $result = $structure->getReference($this->_module, $this->_node);
        if ($result and isset($result['data'][$this->_value]))
            return $result['data'][$this->_value];
        return null;
    }


    public function getTitle()
    {
        $structure = \Alib\Structure::getInstance();
        $result = $structure->getReference($this->_module, $this->_node);
        if ($result)
            return $result['title'];

        return null;
    }


}
