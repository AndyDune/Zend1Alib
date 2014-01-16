<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 26.06.12
 * Time: 10:31
 *
 * Основа длдя моделей - сервисов.
 * Эти модели не саязаны с данными из базы данных.
 *
 */
namespace Alib\Model\AbstractClass;
use Alib\Exception;
abstract class Service
{

    /**
     * Имя текущего моделя.
     * Текущий - это модуль в котором находится модель.
     *
     * @var string
     */
    protected $_moduleName = '';


    public function __construct()
    {
        $this->init();
    }

    public function init()
    {

    }

}
