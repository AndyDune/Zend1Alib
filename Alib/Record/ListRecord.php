<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.09.12
 * Time: 17:11
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record;
use Alib\Session;
class ListRecord
{

    /**
     * @var AbstractClass\Record
     */
    protected $_recordObject;


    /**
     * @var \Zend_Session_Namespace
     */
    protected $_session;

    /**
     * @var \Alib\Db\SelectSimple
     */
    protected $_select;


    public function __construct(AbstractClass\Record $recordObject)
    {
        $this->_recordObject = $recordObject;
        $this->_select = $recordObject->getTable()->getSelectSimple();
        $this->_session = Session::getNamespace('record-list-data');
    }

    public function count()
    {
        return $this->_select->count();
    }

    public function setLimits($limit, $shift)
    {
        return $this->_select->get($limit, $shift);
    }

}
