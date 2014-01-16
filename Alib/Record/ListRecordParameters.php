<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.09.12
 * Time: 17:51
 * To change this template use File | Settings | File Templates.
 */
namespace Alib\Record;
use Alib\Session;
class ListRecordParameters
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


    /**
     * Количество записей на странице.
     *
     * @var int
     */
    protected $_perPage = 20;

    /**
     * @var \Alib\Controller\Action
     */
    protected $_controller;

    protected $_addRequestParams = [];


    protected $_excludedFilter = [];

    public function __construct(AbstractClass\Record $recordObject, $controller)
    {
        $this->_controller = $controller;
        $this->_recordObject = $recordObject;
        $this->_select = $recordObject->getTable()->getSelectSimple();
        $this->_session = Session::getNamespace('record-list-data');
    }

    public function setPerPage($value)
    {
        $this->_perPage = $value;
        return $this;
    }


    public function getSelect()
    {
        return $this->_select;
    }

    public function setAddRequestParams($data)
    {
        $this->_addRequestParams = $data;
        return $this;
    }

    public function excludeFilter($filter)
    {
        $this->_excludedFilter = [$filter];
        return $this;
    }



    public function buildSelect()
    {
        $order_field = $this->_controller->getParam('order_field', null);

        if ($order_field)
        {
            $order_direction = $this->_controller->getParam('order_direction', null);
            $this->_select->addOrder($order_field, $order_direction);
        }

        $filter = $this->_controller->getParam('filter', null);
        if (!is_array($filter))
        {
            $filter = [];
        }
        if (isset($this->_addRequestParams['filter']) and is_array($this->_addRequestParams['filter']))
            $filter = $filter + $this->_addRequestParams['filter'];

        if (count($this->_excludedFilter))
        {
            foreach($this->_excludedFilter as $value)
            {
                unset($filter[$value]);
            }
        }

        if (count($filter))
        {
            foreach($filter as $field => $value)
            {
                if ($value === '')
                    continue;
                $format = $this->_recordObject->getDataFormat($field);
                if (!$format or !isset($format['class']) or
                     (isset($format['type']) and $format['type'] == 'no-data')
                )
                    continue;
                if ($format['class'] == 'String'
                    or $format['class'] == 'Text'
                    or $format['class'] == 'Login'
                    or $format['class'] == 'Email'
                    or $format['class'] == 'ReferenceStructure'
                )
                {
                    $this->_select->addFilterAnd($field, '%' . $value . '%', 'LIKE');
                }
                else
                    $this->_select->addFilterAnd($field, $value, '=', '?i');
            }
        }

        return $this;
    }

    public function getData()
    {
        return $this->_select->get($this->_perPage, $this->_getShiftWithPageParameter());
    }

    protected function _getShiftWithPageParameter()
    {
        $page = (int)$this->_controller->getParam('page', 0);
        if ($page > 0)
            $page--;
        return $page *  $this->_perPage;

    }
}
