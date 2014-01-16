<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 23.04.12
 * Time: 18:15
 *
 * От 2012-09-03 не используется.
 */
namespace Alib\Db\Traits;
trait DbAdapter
{
    /**
     * @var \Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    protected function _formatTableName($table)
    {
        $table = $this->_adapter->quoteTableAs($table);
        return $table;
    }
}
