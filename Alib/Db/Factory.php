<?php
/* 
 * Фабрика таблиц
 *
 * История:
 *   2011-09-05 table() может принимать объект типа Select
 *   2011-07-27 Вызов метода при возврате объекта из репозитория. 
 *   2011-07-26 Доработан метод select(). Аргумент group может быть реализацией \rzn\model\db\abs\www\Select
 *   2011-04-20 Бобавлено наследование параметров от объекта в нруппе в ::select
 *
 */
namespace Alib\Db;
use Alib;
class Factory
{

    protected static $_table = array();
    protected static $_select = array();

    /**
     * Уже созданные обзекты групп таблиц БД.
     * Ключем является совокупность названия группы и реализации.
     *
     * @var array
     */
    protected static $_group = array();

    protected static $_prefix = 'Alib';
    protected static $_prefixTable = 'rznw';
    protected static $_module = 'www';

    protected static $_groupStore = null;



    public static function setRealization($realization)
    {
        
    }

    public static function setPrefix($prefix)
    {
        static::$_prefix = $prefix;
    }


    /**
     * TODO Сделать работу с группами
     *
     * @static
     * @param $name
     * @param string $realization
     * @return Group
     */
    public static function group($name, $realization = 'base')
    {
        $groupName = $name . '+' . $realization;
        if (isset(static::$_group[$groupName]))
            return static::$_group[$groupName];
        static::$_group[$groupName] = new Group($name, $realization, static::$_prefixTable);
        static::$_group[$groupName]->setAdapter(\Zend_Db_Table_Abstract::getDefaultAdapter());
        return static::$_group[$groupName];
    }


    /**
     * Выбрать объект для работы данными таблицы.
     * Вставка, удаление, обновление.
     *
     * @param string $name
     * @param string $group
     * @param string $realization
     * @return rzn\model\db\abs\www\Data
     */
    public static function table($name, $group, $realization = 'base')
    {
        if ($group instanceof Alib\Db\First\Abs\Table)
        {
            $realization = $group->getRealization();
            $group =  $group->getGroup();
        }
        else if ($group instanceof Alib\Db\First\Abs\Select)
        {
            $table_object = $group->getTableObject();
            $realization = $table_object->getRealization();
            $group =  $table_object->getGroup();
        }
        

        $alias = $name . '*' . $group . '*' . $realization . '*' . static::$_module;
        if (isset(static::$_table[$alias]))
        {
            static::$_table[$alias]->clear();
            $object = static::$_table[$alias];
        }
        else
        {
            $prefix =  static::$_prefix . '\\Db\\First\\Table\\' . static::formatName($name, $group);
            $object = new $prefix($name, $group, $realization, static::$_prefixTable);
            static::$_table[$alias] = $object;
        }
        $object->initFactory();
        return $object;
    }

    /**
     * Выбрать объект для работы со списком.
     *
     * @param string $name
     * @param string $group
     * @param string $realization
     * @return rzn\model\db\abs\www\Select
     */
    public static function select($name, $group, $realization = 'base')
    {
        if ($group instanceof Alib\Db\First\Abs\Table)
        {
            $realization = $group->getRealization();
            $group =  $group->getGroup();
        }
        else if ($group instanceof Alib\Db\First\Abs\Select)
        {
            $table_object = $group->getTableObject();
            $realization = $table_object->getRealization();
            $group =  $table_object->getGroup();
        }


        $alias = $name . '*' . $group . '*' . $realization . '*' . static::$_module;
        if (isset(static::$_select[$alias]))
        {
            static::$_select[$alias]->clear();
            $object = static::$_select[$alias];
        }
        else
        {
            $table =  static::table($name, $group, $realization);
            $prefix =  static::$_prefix . '\\Db\\First\\Select\\' . static::formatName($name, $group);
            $object = new $prefix($table);
            static::$_select[$alias] = $object;
        }
        $object->initFactory();
        return $object;
    }

    

    /**
     * Синтезирует имя класа из 2-х слов, которые входяти  в имя таблицы
     *
     * На входе
     *   $name = data
     *   $group = news-articles
     * На выходе
     *  NewsArticle_Data
     *
     * @param string $name data
     * @param string $group news-articles
     * @return string NewsArticle_Data
     */
    protected static function formatName($name, $group)
    {
        return static::formatNamePart($group) . '\\' . static::formatNamePart($name);
    }

    /**
     * Превращает строку вида:
     * one-two-three в OneTwoThree
     * one -> One
     *
     * @param string $name
     * @return string
     */
    protected static function formatNamePart($name)
    {
        $arr = explode('-', $name);
        $result = '';
        foreach($arr as $key => $value)
        {
            $result .= ucfirst($value);
        }
        return $result;
    }
    
    public static function storeGroup($group)
    {
        if($group)
        {
            static::$_groupStore = $group;
            return $group;
        }
        if (!static::$_groupStore)
            throw new Alib\Exception ('Имя группа таблий не сохранена ранее в классе ' . __CLASS__, 1);
        return static::$_groupStore;
    }


}

