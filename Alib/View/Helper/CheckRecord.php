<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 21.09.12
 * Time: 14:37
 *
 * Проверка на соответсвие переменной вида на тип.
 * Тип должен быть
 *
 */
namespace Alib\View\Helper;
use Alib\Record;
class CheckRecord extends \Alib\View\HelperAbstract
{
    /**
     * @var Record\AbstractClass\Record
     */
    protected $_record = null;

    public function direct($field = null)
    {
        if (!$this->view->$field instanceof Record\AbstractClass\Record)
        {
            throw new \Alib\Exception('Переменная "' . $field . '" не соответсвует типу Alib\\Record\\AbstractClass\\Record', 1);
        }
        $this->_record = $field;
        return $this;
    }

    public function getData()
    {
        return $this->_record->getDataObject();
    }
}