<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 29.10.12
 * Time: 14:14
 *
 *
 */
namespace Alib\Db\Traits;
trait SelectWhereAccumulatorMethods
{
    protected $_filter = [];
    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @param <mixed> $value значение
     * @param <string> $state плейсхолдер для замены
     * @param <string> $comp соотношение (=, >, <, <>)
     * @return SelectSimple
     */
    public function addFilterAnd($field, $value, $comp = '=', $state = '?')
    {
        if ($state == '?i')
            $value = (int)$value;
        else
            $value = (string)$value;
        $data = array('and', $field, $value, $comp);
        $this->_filter[] = $data;
        return $this;
    }


    public function addFilterOr($field, $value, $comp = '=', $state = '?')
    {
        if ($state == '?i')
            $value = (int)$value;
        else
            $value = (string)$value;
        $data = array('or', $field, $value, $comp);
        $this->_filter[] = $data;
        return $this;
    }


    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @return SelectSimple
     */
    public function addFilterNullAnd($field)
    {
        $value = null;
        $data = array('and null', $field, null, null);
        $this->_filter[] = $data;
        return $this;
    }

    /**
     * Установка фильтра.
     *
     * @param <string> $field поле в таблице
     * @return SelectSimple
     */
    public function addFilterNotNullAnd($field)
    {
        $value = null;
        $data = array('and notnull', $field, null, null);
        $this->_filter[] = $data;
        return $this;
    }

    /**
     * Добавление в фильтра конструкции IN (?)
     *
     *
     * @param string $field
     * @param array $value
     * @return SelectSimple
     */
    public function addFilterIn($field, $value)
    {
        //$field = $this->_formatFieldName($field);

        if (!is_array($value))
            throw new \Alib\Exception ('Должен быть массив', 0);

        $data = array('and in', $field, $value);
        $this->_filter[] = $data;
        return $this;
    }


    public function clearWhere()
    {
        $this->_filter = [];
        return $this;
    }
}
