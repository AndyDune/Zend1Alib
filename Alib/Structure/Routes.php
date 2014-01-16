<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Dune
 * Date: 24.02.12
 * Time: 17:30
 *
 * Обработка локального файла с маршрутами.
 *
 */
namespace Alib\Structure;
class Routes
{
    protected $_routesFullFilename = '';

    /**
     * @var \Alib\Structure
     */
    protected $_structure = null;

    /**
     * @var \Alib\Structure\Level
     */
    protected $_level = null;


    /**
     * @var \Zend_Controller_Router_Rewrite|null
     */
    protected $_router = null;
    public function __construct(\Alib\Structure\Level $level, \Alib\Structure $structure)
    {
        $this->_router = $structure->getRouter();
        $this->_structure = $structure;
        $this->_level = $level;
    }


    /**
     * Запуск обработки марштутов уровня.
     *
     * @return bool|int Количесво маршрутов или false
     */
    public function process()
    {
        $this->_routesFullFilename = $this->_level->getConfigFileFullName($this->_structure->getFileNameRoutes());
        if (!is_file($this->_routesFullFilename))
            return false;
        $config = new \Zend_Config_Ini($this->_routesFullFilename, 'base', true);
        /**
         * Количество загруженных маршрутов
         */
        $count_route = 0;
        foreach($config as $value => $info)
        {
            $count_route++;
            $class = (isset($info->type)) ? $info->type : 'Zend_Controller_Router_Route';
            if (!class_exists($class))
            {
                \Zend_Loader::loadClass($class);
            }

            if (!$info->defaults->action)
                $info->defaults->action = 'index';


            if (!$info->defaults->module)
                $info->defaults->module = 'Www';
            else
                $info->defaults->module = ucfirst($info->defaults->module);
            $real_path = $this->_level->getUrl();
            $info->route = $real_path . '/' . ltrim($info->route, '/');

            $route = call_user_func(array($class, 'getInstance'), $info);

            // Изменяем название для исключеия возможного конфликта
            $this->_router->addRoute($real_path . $value, $route);

        }
        return $count_route;
    }
}